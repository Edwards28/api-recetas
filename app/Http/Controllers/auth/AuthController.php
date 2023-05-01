<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    private function validations()
    {
        // Validaciones.
        return [
            'correo_electronico' => ['required'],
            'contrasena' => ['required'],
        ];
    }

    public function login(Request $request)
    {
        // Obtenemos datos.
        $data = $request->all();
        $hoy = Carbon::now();
        // Validamos.
        $validator = Validator::make($data, $this->validations());
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 400);
        }
        // Buscamos usuario por código y contraseña.
        $user = Usuario::where('correo_electronico', $data['correo_electronico'])->first();
        // Validamos credenciales.
        if (!$user || !Hash::check($data['contrasena'], $user->contrasena)) {
            return response()->json([
                'success' => false,
                'message' => '¡Correo o contraseña incorrectos!',
            ], 404);
        }
        // Creando token.
        $token = $user->createToken('auth_token');
        $token->accessToken->expires_at = Carbon::now()->addHours(3);
        $token->accessToken->save();
        // Retornando respuesta.
        return response()->json([
            'success' => true,
            'message' => '¡Usuario autenticado exitósamente!',
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($token->accessToken->expires_at)->toDateTimeString(),
            'usuario' => $user->id,
        ], 200);
    }

    public function logout(Request $request)
    {
        // Obtenemos token.
        $request->user()->currentAccessToken()->delete();
        // Retornando respuesta.
        return response()->json([
            'success' => true,
            'message' => '¡Sesión cerrada exitósamente!',
        ], 200);
    }
}
