<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Favorito;
use App\Models\Receta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Expr\Cast\Object_;

class RecetaController extends Controller
{
    private function validations()
    {
        // Validaciones.
        return [
            'nombre' => ['required', 'string'],
            'descripcion' => ['required', 'string'],
            'categoria_id' => ['required', 'integer'],
            'tiempo_preparacion' => ['required', 'integer'],
            'porciones' => ['required', 'integer'],
            'usuario_id' => ['required', 'integer'],
            'ingredientes' => ['required', 'array'],
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $params = $request->all();

        $sql = 'SELECT
        a.id, a.nombre, a.descripcion, b.nombre categoria,
        CONCAT_WS(" ",a.tiempo_preparacion, "Minutos") tiempo_preparacion,
        CASE WHEN a.porciones = 1 THEN CONCAT_WS(" ",a.porciones, "Porción") ELSE CONCAT_WS(" ",a.porciones, "Porciones") END porciones,
        a.imagen, c.nombre usuario,
        CASE WHEN ((SELECT COUNT(*) FROM favoritos d WHERE d.receta_id = a.id AND d.usuario_id = ?)) = 0 THEN
            FALSE
        ELSE
            TRUE
        END contador_favoritos
        FROM recetas a
        LEFT JOIN categorias b ON a.categoria_id = b.id
        LEFT JOIN usuarios c ON a.usuario_id = c.id
        WHERE a.usuario_id != ?';

        if (array_key_exists('nombre', $params)) {
            $sql .= ' AND a.nombre LIKE "%' . $params['nombre'] . '%"';
        }

        if (array_key_exists('categoria_id', $params)) {
            $sql .= ' AND a.categoria_id = ' . $params['categoria_id'];
        }

        if (array_key_exists('tiempo_preparacion', $params)) {
            $sql .= ' AND a.tiempo_preparacion = ' . $params['tiempo_preparacion'];
        }

        $recetas = DB::select($sql, [$request->user()->id, $request->user()->id]);

        return response()->json([
            'success' => true,
            'recetas' => $recetas
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Obtenemos datos.
            $data = $request->all();
            $data['usuario_id'] = $request->user()->id;
            // Validamos.
            $validator = Validator::make($data, $this->validations());
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 400);
            }
            // Creando un registro.
            $receta = Receta::create($data);
            $receta->save();
            // Guardando ingredientes.
            foreach ($data['ingredientes'] as $ingrediente) {
                DB::insert('INSERT INTO recetas_ingredientes (receta_id, ingrediente_id, cantidad) VALUES (?, ?, 1)', [$receta->id, $ingrediente]);
            }
            // Retornando respuesta.
            return response()->json([
                'success' => true,
                'message' => '¡Receta creada exitósamente!',
            ], 201);
        } catch (\Exception $e) {
            // Retornando respuesta del error.
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function showByUser(string $id)
    {
        $recetas = DB::select('SELECT
        a.id, a.nombre, a.descripcion, b.nombre categoria,
        CONCAT_WS(" ",a.tiempo_preparacion, "Minutos") tiempo_preparacion,
        CASE WHEN a.porciones = 1 THEN CONCAT_WS(" ",a.porciones, "Porción") ELSE CONCAT_WS(" ",a.porciones, "Porciones") END porciones,
        a.imagen, c.nombre usuario
        FROM recetas a
        LEFT JOIN categorias b ON a.categoria_id = b.id
        LEFT JOIN usuarios c ON a.usuario_id = c.id
        WHERE a.usuario_id = ?;', [$id]);

        return response()->json([
            'success' => true,
            'recetas' => $recetas
        ]);
    }

    public function showFavoritesByUser(string $id)
    {
        $recetas = DB::select('SELECT
        a.id, b.nombre, b.descripcion, c.nombre categoria,
        CONCAT_WS(" ",b.tiempo_preparacion, "Minutos") tiempo_preparacion,
        CASE WHEN b.porciones = 1 THEN CONCAT_WS(" ",b.porciones, "Porción") ELSE CONCAT_WS(" ",b.porciones, "Porciones") END porciones,
        b.imagen, d.nombre usuario
        FROM favoritos a
        LEFT JOIN recetas b ON a.receta_id = b.id
        LEFT JOIN categorias c ON b.categoria_id = c.id
        LEFT JOIN usuarios d ON b.usuario_id = d.id
        WHERE a.usuario_id = ?;', [$id]);

        return response()->json([
            'success' => true,
            'recetas' => $recetas
        ]);
    }

    public function saveFavorite(Request $request)
    {
        try {
            // Obtenemos datos.
            $data = $request->all();
            $data['usuario_id'] = $request->user()->id;
            // Validamos.
            $validator = Validator::make($data, [
                'receta_id' => ['required', 'integer'],
                'usuario_id' => ['required', 'integer'],
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 400);
            }
            // Creando un registro.
            DB::insert('INSERT INTO favoritos (receta_id, usuario_id) VALUES (?, ?)', [$data['receta_id'], $data['usuario_id']]);
            // Retornando respuesta.
            return response()->json([
                'success' => true,
                'message' => '¡Receta agregada a favoritas exitósamente!',
            ], 201);
        } catch (\Exception $e) {
            // Retornando respuesta del error.
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $recetaDb = Receta::find($id);
        $receta = (object)[];
        $receta->id = $recetaDb->id;
        $receta->nombre = $recetaDb->nombre;
        $receta->descripcion = $recetaDb->descripcion;
        $receta->categoria_id = $recetaDb->categoria_id;
        $receta->tiempo_preparacion = $recetaDb->tiempo_preparacion;
        $receta->porciones = $recetaDb->porciones;
        $receta->ingredientes = collect(DB::select('SELECT id FROM recetas_ingredientes WHERE receta_id = ?', [$id]))->pluck('id');

        return response()->json([
            'success' => true,
            'receta' => $receta
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Obtenemos datos.
            $data = $request->all();
            $data['usuario_id'] = $request->user()->id;
            // Validamos.
            $validator = Validator::make($data, $this->validations());
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 400);
            }
            // Actualizando un registro.
            $receta = Receta::find($id);
            $receta->nombre = $data['nombre'];
            $receta->descripcion = $data['descripcion'];
            $receta->categoria_id = $data['categoria_id'];
            $receta->tiempo_preparacion = $data['tiempo_preparacion'];
            $receta->porciones = $data['porciones'];
            $receta->save();
            // Eliminando ingredientes.
            DB::delete('DELETE FROM recetas_ingredientes WHERE receta_id = ?', [$id]);
            // Guardando ingredientes.
            foreach ($data['ingredientes'] as $ingrediente) {
                DB::insert('INSERT INTO recetas_ingredientes (receta_id, ingrediente_id, cantidad) VALUES (?, ?, 1)', [$receta->id, $ingrediente]);
            }
            // Retornando respuesta.
            return response()->json([
                'success' => true,
                'message' => '¡Receta actualizada exitósamente!',
            ], 200);
        } catch (\Exception $e) {
            // Retornando respuesta del error.
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $receta = Receta::find($id);
        if ($receta) {
            $receta->delete();
            return response()->json([
                'success' => true,
                'message' => '¡Receta eliminada exitósamente!',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => '¡Receta no encontrada!',
            ], 404);
        }
    }

    public function destroyFavorite(string $id)
    {
        //
        $favorita = Favorito::find($id);
        if ($favorita) {
            $favorita->delete();
            return response()->json([
                'success' => true,
                'message' => '¡Receta favorita eliminada exitósamente!',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => '¡Receta favorita no encontrada!',
            ], 404);
        }
    }
}
