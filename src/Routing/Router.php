<?php declare(strict_types=1);

namespace Julius\Framework\Routing;

use Julius\Framework\Http\Interface\RequestInterface;

class Router
{
    private static RequestInterface $request;

    private static bool    $routeMatched;
    private static array   $groupStack;

    public static function boot(RequestInterface $request)
    {
        self::$request = &$request;

        self::$routeMatched = false;
        self::$groupStack   = [];
    }

    public static function add(string $method, string $uri, array | callable $handler, array $regex = []) : void
    {    
        // Verifica se já foi encontrado uma rota ou se o método é o mesmo que requisição
        if(!self::$routeMatched || strcasecmp(self::$request->getMethod(), $method) === 0)
        {
            $route = trim(self::getUriGroupStack() . $uri, '/');
            
            $pattern = self::processRegex($route, $regex);
            
            if($parameters = self::matchRoute($pattern))
            {
                array_shift($parameters);

                self::invoke($handler, $parameters);
            }
        }
    }

    public static function group(string $prefix, callable $callback) : void
    {
        if(!self::$routeMatched)
        {
            self::$groupStack[] = trim($prefix, '/');

            $groupUri   = explode('/', self::getUriGroupStack());
            $requestUri = explode('/', self::$request->getUri());

            if(self::compareArrayDifference($groupUri, $requestUri))
            {
                $callback();
            }

            array_pop(self::$groupStack);
        }
    }

    public static function fallback(array | callable $handler) : void
    {
        if(!self::$routeMatched)
        {
            self::invoke($handler, []);
        }
    }

    public static function get(string $uri, array | callable $handler, array $regex = []) : void
    {
        self::add('GET', $uri, $handler, $regex);
    }

    public static function post(string $uri, array | callable $handler, array $regex = []) : void
    {
        self::add('POST', $uri, $handler, $regex);
    }

    public static function put(string $uri, array | callable $handler, array $regex = []) : void
    {
        self::add('PUT', $uri, $handler, $regex);
    }

    public static function patch(string $uri, array | callable $handler, array $regex = []) : void
    {
        self::add('PATCH', $uri, $handler, $regex);
    }

    public static function delete(string $uri, array | callable $handler, array $regex = []) : void
    {
        self::add('DELETE', $uri, $handler, $regex);
    }

    public static function options(string $uri, array | callable $handler, array $regex = []) : void
    {
        self::add('OPTIONS', $uri, $handler, $regex);
    }

    private static function processRegex(string $uri, array $regex) : string
    {
        foreach ($regex as $key => $pattern)
        {
            $uri = str_replace(':'. $key, $pattern, $uri);
        }

        return preg_replace('/:\w+/', '([\w]+)', $uri);
    }

    private static function matchRoute(string $pattern) : false | array
    {
        $uri = self::$request->getUri();

        if(preg_match('#^'.$pattern.'$#', $uri, $parameters))
        {
            return $parameters;
        }

        return false;
    }

    private static function getUriGroupStack() : string
    {
        return implode('/', self::$groupStack);
    }

    private static function compareArrayDifference(array $array1, array $array2) : bool
    {
        $size = sizeof($array1);

        foreach($array1 as $key => $value)
        {
            if(array_key_exists($key, $array2))
            {
                if($array2[$key] == $value || strpos($value, ':') === 0)
                {
                    $size--;
                }
            }
        }

        return $size === 0;
    }

    private static function invoke(array | callable $handler, array $parameters) : void
    {
        self::$routeMatched = true;

        if(is_callable($handler))
        {
            $handler(self::$request, ...$parameters);

            return;
        }

        $controller = new $handler[0](self::$request);

        $controller->{$handler[1]}(...$parameters);
    }
}
