<?php declare(strict_types=1);

namespace Julius\Test\Controllers\Dashboard;

use Julius\Framework\Controllers\Controller;

class DashboardController extends Controller
{
    public function get() : void
    {
        echo 'Controller::DashboardController';
    }
}