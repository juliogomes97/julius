<?php declare(strict_types=1);

namespace Julius\Test\Controllers\Dashboard;

use Julius\Framework\Controllers\Controller;

class PostsController extends Controller
{
    public function get() : void
    {
        echo 'Controller::PostsController';
    }
}