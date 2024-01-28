<?php declare(strict_types=1);

namespace Julius\Framework\Controllers;

use \Julius\Framework\Http\Request;
use \Julius\Framework\Models\Session;

abstract class Controller
{
    protected Request $request;
    protected Session $session;

    public function __construct(Request $request)
    {
        $this->request = $request; 
        $this->session = new Session;   
    }
}