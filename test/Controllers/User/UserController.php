<?php declare(strict_types=1);

namespace Julius\Test\Controllers\User;

use Julius\Framework\Controllers\Controller;

class UserController extends Controller
{
    public function index() : void
    {
        echo 'Controller::UserController';
    }
}