<?php declare(strict_types=1);

namespace Julius\Framework\Routing;

use \Julius\Framework\Http\Request;

class Router
{
    private static bool $found;

    private Request $request;
    private array   $uriArray;

    public function __construct(array $uriArray = [])
    {
        self::$found    = false;

        $this->request  = new Request;
        $this->uriArray = $uriArray;
    }

    /**
     * Cria uma rota
     *
     * @param string    $method     [GET; POST; DELETE; ...]
     * @param string    $uri        Caminho para aceder ao controlador Exemplo: /users
     * @param string    $controller Nome da class do controllador Exemplo: \Julius\Test\Controllers\LandingController::class
     * @param string    $handler    Nome da função ao chamar
     * @param array     $regex      Expresão do parametros Exemplo: ['id' => '([0-9]+)']
     * 
     * @return void
     */
    public function add(string $method, string $uri, string $controller, string $handler = 'get', array $regex = []) : void
    {
        if(self::$found || strcasecmp($this->request->method, $method) !== 0)
            return;

        $uris = $this->uriArray;

        if($uri != '/')
        {
            $uris[] = $uri;
        }

        $route = implode('/', $uris);

        $processed_uri = $this->processRegex($route, $regex);

        $pattern = $this->buildPattern($processed_uri);
        
        if(preg_match('#^'.$pattern.'$#', $this->request->uri, $parameters))
        {
            array_shift($parameters);

            $this->invoke($controller, $handler, $parameters);
        }
    }

    /**
     * Cria uma rota com o metodo 'GET', o nome da função por defeite é 'get()'
     *
     * @param string    $uri        Caminho para aceder ao controlador Exemplo: /users
     * @param string    $controller Nome da class do controllador Exemplo: \Julius\Test\Controllers\LandingController::class
     * @param array     $regex      Expresão do parametros Exemplo: ['id' => '([0-9]+)']
     * 
     * @return void
     */
    public function get(string $uri, string $controller, array $regex = []) : void
    {
        $this->add('GET', $uri, $controller, 'get', $regex);
    }

    /**
     * Cria uma rota com o metodo 'POST', o nome da função por defeite é 'post()'
     *
     * @param string    $uri        Caminho para aceder ao controlador Exemplo: /users
     * @param string    $controller Nome da class do controllador Exemplo: \Julius\Test\Controllers\LandingController::class
     * @param array     $regex      Expresão do parametros Exemplo: ['id' => '([0-9]+)']
     * 
     * @return void
     */
    public function post(string $uri, string $controller, array $regex = []) : void
    {
        $this->add('POST', $uri, $controller, 'post', $regex);
    }

    /**
     * Caso nenhuma rota for encontrada será chamado o fallback()
     *
     * @param string    $controller Nome da class do controllador Exemplo: \Julius\Test\Controllers\NotFoundController::class
     * @param string    $handler    Nome da função ao chamar, por defeito é 'get()'
     * 
     * @return void
     */
    public function fallback(string $controller, string $handler = 'get') : void
    {
        if(self::$found)
            return;
        
        $this->invoke($controller, $handler, []);
    }

    /**
     * Criar grupos de rotas
     *
     * @param string    $uri        Nome pai da rota
     * @param string    $callback   O parametros do callback è do tipo Julius\Framework\Routing\Router
     * 
     * @return void
     */
    public function group(string $uri, callable $callback) : void
    {
        if(self::$found)
            return;

        $uriArray = explode('/', $uri);

        $newUriArray = array_merge($this->uriArray, $uriArray);

        $newUriArraySize = sizeof($newUriArray);

        $rquestUriArray = explode('/', $this->request->uri);

        foreach($newUriArray as $key => $newUriArrayValue)
        {
            if(array_key_exists($key, $rquestUriArray))
            {
                if($rquestUriArray[$key] == $newUriArrayValue || strpos($newUriArrayValue, ':') === 0)
                {
                    $newUriArraySize--;
                }
            }
        }

        if($newUriArraySize === 0)
        {
            $callback(new self($newUriArray));
        }
        else
        {
            $this->uriArray = array_slice($newUriArray, 0, -sizeof($uriArray));
        }
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

        self::$found = true;
    }
}