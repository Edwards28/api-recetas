<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsuarioController extends Controller
{
    private function validations($usuarioId = '')
    {
        // Validaciones.
        $id = $usuarioId ? ', ' . $usuarioId : '';
        return [
            'nombre' => ['required', 'string'],
            'correo_electronico' => ['required', 'email', 'unique:usuarios,correo_electronico' . $id],
            'contrasena' => ['required', 'regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,12}$/'],
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Obtenemos datos.
            $data = $request->all();
            // Validamos.
            $validator = Validator::make($data, $this->validations());
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 400);
            }
            // Creando un registro.
            $data['contrasena'] = bcrypt($data['contrasena']);
            $usuario = Usuario::create($data);
            $usuario->save();
            // Retornando respuesta.
            return response()->json([
                'success' => true,
                'message' => '¡Usuario creado exitósamente!',
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
