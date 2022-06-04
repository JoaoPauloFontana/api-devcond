<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    BilletController,
    DocController,
    FoundAndLostController,
    ReservationController,
    UnitController,
    UserController,
    WallController,
    WarningController,
};

Route::get('/ping', function(){
    return ['pong'=>true];
});

Route::get('/401', [AuthController::class, 'unauthorized'])->name('login');

Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/auth/register', [AuthController::class, 'register'])->name('auth.register');

Route::middleware('auth:api')->group(function(){
    Route::post('/auth/validate', [AuthController::class, 'validateToken'])->name('auth.token');
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

    Route::get('/walls', [WallController::class, 'getAll'])->name('mural.getAll');
    Route::post('/wall/{id}/like', [WallController::class, 'like'])->name('mural.like');

    Route::get('/docs', [DocController::class, 'gelAll'])->name('docs.getAll');

    Route::get('/warnings', [WarningController::class, 'getMyWarnings'])->name('warn.getMyWarnings');
    Route::post('/warning', [WarningController::class, 'setWarning'])->name('warn.setWarning');
    Route::post('/warning/file', [WarningController::class, 'addWarningFile'])->name('warn.file');

    Route::get('/billets', [BilletController::class, 'getAll'])->name('billet.getAll');

    Route::get('/foundandlost', [FoundAndLostController::class, 'getAll'])->name('fandl.getAll');
    Route::post('/foundandlost', [FoundAndLostController::class, 'insert'])->name('fandl.insert');
    Route::put('/foundandlost/{id}', [FoundAndLostController::class, 'update'])->name('fandl.update');

    Route::get('/unit/{id}', [UnitController::class, 'getInfo'])->name('unit.getInfo');
    Route::post('/unit/{id}/addperson', [UnitController::class, 'addPerson'])->name('unit.addPerson');
    Route::post('/unit/{id}/addvehicle', [UnitController::class, 'addVehicle'])->name('unit.addVehicle');
    Route::post('/unit/{id}/addpet', [UnitController::class, 'addPet'])->name('unit.addPet');
    Route::post('/unit/{id}/removeperson', [UnitController::class, 'removePerson'])->name('unit.removePerson');
    Route::post('/unit/{id}/removevehicle', [UnitController::class, 'removeVehicle'])->name('unit.removeVehicle');
    Route::post('/unit/{id}/removepet', [UnitController::class, 'removePet'])->name('unit.removePet');

    Route::get('/reservations', [ReservationController::class, 'getReservations'])->name('reservation.getReservations');
    Route::post('/reservation/{id}', [ReservationController::class, 'setReservation'])->name('setReservation');
    Route::get('/reservation/{id}/disableddates', [ReservationController::class, 'getDisabledDates'])->name('reservation.getDisabledDates');
    Route::get('/reservation/{id}/times', [ReservationController::class, 'getTimes'])->name('reservation.getTimes');
    Route::get('/myreservations', [ReservationController::class, 'getMyReservation'])->name('reservation.getMyReservation');
    Route::delete('/myreservation/{id}', [ReservationController::class, 'delMyReservation'])->name('delMyReservation');
});
