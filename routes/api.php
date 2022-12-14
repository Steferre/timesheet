<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/contracts', 'App\Http\Controllers\Api\ContractController@index');
Route::get('/contracts/filter', 'App\Http\Controllers\Api\ContractController@filter');
// rotta api per i ticket
Route::get('/tickets', 'App\Http\Controllers\Api\TicketController@index');
Route::get('/tickets/getCDCs', 'App\Http\Controllers\Api\TicketController@getCDCs');