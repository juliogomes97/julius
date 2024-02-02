<?php declare(strict_types=1);

namespace Julius\Framework\Controllers;

use \Julius\Framework\Http\Request;
use \Julius\Framework\Models\Session;

abstract class Controller
{
    protected Request $request;
    protected Session $session;

    public function __construct(Request $request, array $session_params = [])
    {
        $this->request = $request; 
        $this->session = new Session($session_params);   
    }

    public function __call(string $method, $a) 
    {
        echo json_encode([
            'status' => 500,
            'message' => 'O controlador {'. get_class($this) .'} não tem o método {'. $method .'} defenido'
        ]);
    }
}