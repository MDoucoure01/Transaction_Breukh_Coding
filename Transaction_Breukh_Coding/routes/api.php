<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CompteController;
use App\Http\Controllers\TransactionController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


//les Route pour les utilisateur
Route::apiResource('user',UserController::class)->only(['index','store']);
Route::delete('user/{id}',[UserController::class,'supprimerUser']);
Route::get('user/{id}',[UserController::class,'userLister']);


// les Routes pour Compte
Route::apiResource('compte',CompteController::class)->only(['index','store']);
Route::delete('compte/{id}',[CompteController::class,'supprimerCompte']);


//Route pour Effectuer une transaction
Route::post('user/{id}/transaction/depot',[TransactionController::class,'depot']);
Route::post('transaction/user/{id}/retrait',[TransactionController::class,'retrait']);
Route::post('transaction/user/{id}/envoie',[TransactionController::class,'envoie']);


//Route pour charger un user
Route::post('user/charge',[UserController::class,'charge']);