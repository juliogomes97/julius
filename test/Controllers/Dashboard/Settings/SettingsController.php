<?php declare(strict_types=1);

namespace Julius\Test\Controllers\Dashboard\Settings;

use Julius\Framework\Controllers\Controller;

class SettingsController extends Controller
{
    public function get() : void
    {
        echo 'Controller::SettingsController';
    }
}