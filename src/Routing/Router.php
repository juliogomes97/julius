<?php declare(strict_types=1);

namespace Julius\Framework\Routing;

use \Julius\Framework\Http\Request;

class Router
{
    private Request $request;
    private bool    $found;

    public function __construct()
    {
        $this->request  = new Request;
        $this->found    = false;
    }

    public function add(string $method, string $uri, string $controller, string $handler = 'get', array $regex = []) : void
    {
        if($this->found || strcasecmp($this->request->method, $method) !== 0)
            return;

        $processed_uri = $this->processRegex($uri, $regex);

        $pattern = $this->buildPattern($processed_uri);
        
        if(preg_match('#^'.$pattern.'$#', $this->request->uri, $parameters))
        {
            array_shift($parameters);

            $this->invoke($controller, $handler, $parameters);
        }
    }

    public function get(string $uri, string $controller, array $regex = []) : void
    {
        $this->add('GET', $uri, $controller, 'get', $regex);
    }

    public function post(string $uri, string $controller, array $regex = []) : void
    {
        $this->add('POST', $uri, $controller, 'post', $regex);
    }

    public function fallback(string $controller, string $handler = 'get') : void
    {
        if($this->found)
            return;
        
        $this->invoke($controller, $handler, []);
    }

    private function processRegex(string $uri, array $regex) : string
    {
        foreach ($regex as $key => $pattern)
        {
            $uri = str_replace($key, $pattern, $uri);
        }

        return $uri;
    }

    private function buildPattern(string $uri) : string
    {
        return preg_replace('/:\w+/', '([\w]+)', $uri);
    }

    private function invoke(string $controller, string $handler, array $parameters) : void
    {
        $controller_object = new $controller($this->request);

        $controller_object->{$handler}(...$parameters);

        $this->found = true;
    }
}