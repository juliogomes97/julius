<?php declare(strict_types=1);

namespace Julius\Framework\Controllers;

use \Julius\Framework\Http\HttpHandler;
use \Julius\Framework\Http\Request;
use \Julius\Framework\Models\Session;

abstract class MasterController
{
    protected Request       $request;
    protected Session       $session;

    protected function __construct(Request $request, array $session_params = [])
    {
        $this->request      = $request; 
        $this->session      = new Session($session_params); 
    }

    public function __call(string $method, array $arguments) : void
    {
        $httpHandler = new HttpHandler([
            'Content-Type' => 'application/json'
        ]);

        $httpHandler->setStatusCode(HttpHandler::STATUS_INTERNAL_SERVER_ERROR);
        
        $httpHandler->defineHeaders();

        echo json_encode(['message' => 'O controlador '. static::class .' não tem o método '. $method .'() defenido']);
    }
}