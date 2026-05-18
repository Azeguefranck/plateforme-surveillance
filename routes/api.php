<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiCapteurController;

Route::post('/capteurs', [ApiCapteurController::class,'store']);

Route::get('/mesure', [ApiCapteurController::class,'latest']);

Route::get('/historiques', [ApiCapteurController::class,'historique']);

Route::get('/alertes', [ApiCapteurController::class,'alertes']);