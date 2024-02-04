<?php declare(strict_types=1);

namespace Julius\Test\Controllers;

use \Julius\Framework\Http\HttpHandler;
use \Julius\Test\Controllers\Controller;

class LandingController extends Controller
{
    public function index() : void
    {
        self::view('LandingView.html');
    }

    public function post() : void
    {
        self::statusCode(HttpHandler::STATUS_NO_CONTENT);

        self::toJson([
            'hello' => 'world'
        ]);
    }
}