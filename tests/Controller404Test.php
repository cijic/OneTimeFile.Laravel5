<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;

class Controller404Test extends TestCase
{
    public function testIndexIs404()
    {
        $response = $this->call('GET', '/404');
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testIndex()
    {
        $viewContent = view('errors/404')->with('title', 'Page not found')->render();
        $response = $this->call('GET', '/404');
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals($viewContent, $response->getOriginalContent());
    }
}
