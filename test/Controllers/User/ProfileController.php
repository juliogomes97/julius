<?php declare(strict_types=1);

namespace Julius\Test\Controllers\User;

use Julius\Framework\Controllers\Controller;

class ProfileController extends Controller
{
    public function get() : void
    {
        echo 'Controller::ProfileController';
    }
}