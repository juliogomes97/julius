<?php declare(strict_types=1);

namespace Julius\Test\Controllers\User\Settings;

use Julius\Framework\Controllers\Controller;

class PrivacyController extends Controller
{
    public function get() : void
    {
        echo 'Controller::PrivacyController';
    }
}