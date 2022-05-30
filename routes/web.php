<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\CdcController;
use App\Http\Controllers\Auth\ClientController;
use App\Http\Controllers\Auth\ContractController;
use App\Http\Controllers\Auth\TicketController;
use App\Http\Controllers\Auth\ForgotPasswordController;
//use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// questa è la rotta base, mettendo un return view alla contracts.index
// essendo questa rtto sotto auth
// se non sono loggato mi manda al login
// se la sessione è ancora valida mi fa entrare nell'app
Route::get('/', [ContractController::class, 'index'])->middleware(['auth'])->name('contracts.index');
Route::get('/closedContracts', [ContractController::class, 'indexConClose'])->middleware(['auth'])->name('contracts.indexConClose');
Route::post('/contracts', [ContractController::class, 'store'])->middleware(['auth'])->name('contracts.store');
Route::get('/contracts/create', [ContractController::class, 'create'])->middleware(['auth'])->name('contracts.create');
Route::get('/contracts/exportCON', [ContractController::class, 'exportCON'])->middleware(['auth'])->name('contracts.exportCON');
Route::get('/contracts/{id}', [ContractController::class, 'show'])->middleware(['auth'])->name('contracts.show');
Route::patch('/contracts/{id}', [ContractController::class, 'update'])->middleware(['auth'])->name('contracts.update');
Route::get('/contracts/{id}/edit', [ContractController::class, 'edit'])->middleware(['auth'])->name('contracts.edit');
Route::delete('/contracts/{id}', [ContractController::class, 'destroy'])->middleware(['auth'])->name('contracts.destroy');

Auth::routes();

Route::get('/clients', [ClientController::class, 'index'])->middleware(['auth'])->name('clients.index');
Route::post('/clients', [ClientController::class, 'store'])->middleware(['auth'])->name('clients.store');
Route::get('/clients/create', [ClientController::class, 'create'])->middleware(['auth'])->name('clients.create');
Route::get('/clients/{id}', [ClientController::class, 'show'])->middleware(['auth'])->name('clients.show');
Route::patch('/clients/{id}', [ClientController::class, 'update'])->middleware(['auth'])->name('clients.update');
Route::get('/clients/{id}/edit', [ClientController::class, 'edit'])->middleware(['auth'])->name('clients.edit');
Route::delete('/clients/{id}', [ClientController::class, 'destroy'])->middleware(['auth'])->name('clients.destroy');

//Route::get('/contracts', [ContractController::class, 'index'])->middleware(['auth'])->name('contracts.index');


Route::get('/tickets', [TicketController::class, 'index'])->middleware(['auth'])->name('tickets.index');
Route::post('/tickets', [TicketController::class, 'store'])->middleware(['auth'])->name('tickets.store');
Route::get('/tickets/create', [TicketController::class, 'create'])->middleware(['auth'])->name('tickets.create');
Route::get('/tickets/export', [TicketController::class, 'export'])->middleware(['auth'])->name('tickets.export');
Route::get('/tickets/{id}', [TicketController::class, 'show'])->middleware(['auth'])->name('tickets.show');
Route::patch('/tickets/{id}', [TicketController::class, 'update'])->middleware(['auth'])->name('tickets.update');
Route::get('/tickets/{id}/edit', [TicketController::class, 'edit'])->middleware(['auth'])->name('tickets.edit');
Route::delete('/tickets/{id}', [TicketController::class, 'destroy'])->middleware(['auth'])->name('tickets.destroy');


Route::get('/cdcs', [CdcController::class, 'index'])->middleware(['auth'])->name('cdcs.index');
Route::post('/cdcs', [CdcController::class, 'store'])->middleware(['auth'])->name('cdcs.store');
Route::get('/cdcs/create', [CdcController::class, 'create'])->middleware(['auth'])->name('cdcs.create');
Route::get('/cdcs/{id}', [CdcController::class, 'show'])->middleware(['auth'])->name('cdcs.show');
Route::patch('/cdcs/{id}', [CdcController::class, 'update'])->middleware(['auth'])->name('cdcs.update');
Route::get('/cdcs/{id}/edit', [CdcController::class, 'edit'])->middleware(['auth'])->name('cdcs.edit');
Route::delete('/cdcs/{id}', [CdcController::class, 'destroy'])->middleware(['auth'])->name('cdcs.destroy');


//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
// ROTTE PER IL RECUPERO DELLA PASSWORD
Route::get('forget-password', [ForgotPasswordController::class, 'showForgetPasswordForm'])->name('forget.password.get');
Route::post('forget-password', [ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('forget.password.post');
Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');
Route::post('reset-password', [ForgotPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');

/* Route::get('myLogin', [LoginController::class, 'showLoginPage'])->name('login.custom.get');
Route::post('myLogin', [LoginController::class, 'sendLoginForm'])->name('login.custom.post'); */