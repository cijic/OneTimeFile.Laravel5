<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// Home page.
Route::get('/', 'ControllerUpload@index');

// Page with status code 404.
Route::get('404', 'Controller404@index');

// POST handler for uploading file.
Route::post('do_upload', 'ControllerUpload@uploadFile');

// Route for checking if user is banned.
Route::post('checkban', 'ControllerUpload@checkBanlistContains');

// Route for downloadable files.
Route::get('{downloadable_url}', [
    'uses' => 'ControllerRoute@route'
]);