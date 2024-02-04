<?php declare(strict_types=1);

namespace Julius\Test\Controllers;

use \Julius\Framework\Http\HttpHandler;
use \Julius\Test\Controllers\Controller;

class NotFoundController extends Controller
{
    public function index() : void
    {
        self::statusCode(HttpHandler::STATUS_NOT_FOUND);

        self::view('NotFoundView.html');
    }
}