<?php

use Illuminate\Support\Facades\Route;
use Trax\Auth\TraxAuth;
use Trax\Lrs\BasicClients\BasicClientController;

Route::get('/lrs/{any?}', function () {
    return view('trax-front-lrs::app');
})->where('any', '.*');

Route::apiResource(
    'trax/api/{source}/basic-clients',
    BasicClientController::class
)->middleware(
    TraxAuth::userMiddleware()
);
