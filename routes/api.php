<?php

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

Route::prefix('v1')->group(function () {
    Route::post('/login', 'API\AuthController@login');
    Route::post('/register', 'API\AuthController@registeredByUser');

    // protected routes by Sanctum
    Route::group(['middleware' => 'auth:sanctum'], function () {
        // use regular routes to determine which function to executed
        Route::post('/adminregistered', 'API\AuthController@registeredByAdmin');
        Route::get('/logout', 'API\AuthController@logout');
        Route::get('/users', 'API\UserController@index');

        // use resource to use regular CRUD
        Route::resource('products', 'API\ProductController');
    });
});
