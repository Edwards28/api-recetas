<?php

use App\Http\Controllers\api\CategoriaController;
use App\Http\Controllers\api\IngredienteController;
use App\Http\Controllers\api\RecetaController;
use App\Http\Controllers\api\UsuarioController;
use App\Http\Controllers\auth\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [UsuarioController::class, 'store']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::group(['prefix' => 'recetas'], function () {
        Route::get('/', [RecetaController::class, 'index']);
        Route::get('/usuario/{idUsuario}', [RecetaController::class, 'showByUser']);
        Route::post('/', [RecetaController::class, 'store']);
        Route::post('/guardar/favorita', [RecetaController::class, 'saveFavorite']);
        Route::get('/favoritas/usuario/{idUsuario}', [RecetaController::class, 'showFavoritesByUser']);
        Route::get('/{id}', [RecetaController::class, 'show']);
        Route::put('/{id}', [RecetaController::class, 'update']);
        Route::delete('/{id}', [RecetaController::class, 'destroy']);
        Route::delete('/favorita/{id}', [RecetaController::class, 'destroyFavorite']);
    });
    Route::group(['prefix' => 'categorias'], function () {
        Route::get('/', [CategoriaController::class, 'index']);
    });
    Route::group(['prefix' => 'ingredientes'], function () {
        Route::get('/', [IngredienteController::class, 'index']);
    });
});
