<?php declare(strict_types=1);

namespace Julius\Test\Controllers;

use Julius\Framework\Controllers\Controller;

class NotFoundController extends Controller
{
    public function get() : void
    {
        echo 'Controller::NotFoundController';
    }
}