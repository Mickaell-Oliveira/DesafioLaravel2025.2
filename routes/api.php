<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CepController;

Route::get('/cep/{cep}',[CepController::class, 'show']);
