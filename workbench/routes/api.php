<?php

use App\Http\Controllers\ExampleScopedController;
use Illuminate\Support\Facades\Route;

Route::resource('/example-scoped', ExampleScopedController::class)
    ->middleware('auth:api');