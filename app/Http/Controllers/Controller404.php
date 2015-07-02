<?php namespace App\Http\Controllers;

class Controller404 extends OneTimeFileBaseController
{
    public function index()
    {
        return response(view('errors/404')->with('title', 'Page not found'), 404);
    }
}