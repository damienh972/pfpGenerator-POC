<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\LayerController;

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


Route::middleware('client.auth')->group(
    function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/layers/{layerId}/addImage', [LayerController::class, 'addImageToLayer']);
        Route::put('/layers/{layerId}/updateImage/{cid}', [LayerController::class, 'updateImageInLayer']);
        Route::delete('/layers/{layerId}/removeImage/{cid}', [LayerController::class, 'removeImageFromLayer']);
        Route::resource('user', UserController::class)->except(['create', 'edit']);
        Route::resource('project', ProjectController::class)->except(['create', 'edit']);
        Route::resource('layer', LayerController::class)->except(['create', 'edit']);
    }
);
