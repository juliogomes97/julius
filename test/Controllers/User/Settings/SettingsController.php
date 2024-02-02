<?php declare(strict_types=1);

namespace Julius\Test\Controllers\User\Settings;

use Julius\Framework\Controllers\Controller;

class SettingsController extends Controller
{
    public function index() : void
    {
        echo 'Controller::SettingsController';
    }
}