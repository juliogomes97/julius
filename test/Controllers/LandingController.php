<?php declare(strict_types=1);

namespace Julius\Test\Controllers;

use Julius\Framework\Controllers\Controller;

class LandingController extends Controller
{
    public function index() : void
    {
        echo 'Controller::LandingController';
    }
}