<?php
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ControllerUploadTest extends TestCase
{
    public function testIndexIsOk()
    {
        $this->visit('/')->assertResponseOk();
    }

    public function testIndex()
    {
        $this->visit('/')->see('Drop here');
    }
}
