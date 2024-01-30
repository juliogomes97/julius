<?php declare(strict_types=1);

namespace Julius\Framework\Routing;

use \Julius\Framework\Http\Request;

class Router
{
    private Request $request;
    private bool    $found;

    private array   $current_group_uri_array;
    private string  $current_group_uri;

    public function __construct()
    {
        $this->request  = new Request;
        $this->found    = false;

        $this->current_group_uri_array  = [];
        $this->current_group_uri        = '';
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
        if($this->found || strcasecmp($this->request->method, $method) !== 0)
            return;
        
        $uri = trim($this->current_group_uri.$uri, '/');

        $processed_uri = $this->processRegex($uri, $regex);

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
        if($this->found)
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
        if($this->found)
            return;

        $uri = $this->current_group_uri.$uri;

        $request_uri_array  = explode('/', $this->request->uri);
        $group_uri_array    = explode('/', trim($uri, '/'));

        foreach($request_uri_array as $key => $current_uri)
        {
            if(array_key_exists($key, $this->current_group_uri_array) && $current_uri == $this->current_group_uri[$key])
            {
                continue;
            }

            if(array_key_exists($key, $group_uri_array) && strpos($group_uri_array[$key], ':') === 0)
            {
                $this->current_group_uri_array[$key] = $group_uri_array[$key];

                continue;
            }

            if(array_key_exists($key, $group_uri_array) && $current_uri == $group_uri_array[$key])
            {
                $this->current_group_uri_array[$key] = $current_uri;
            }
        }

        if($group_uri_array === $this->current_group_uri_array)
        {
            $this->current_group_uri = implode('/', $group_uri_array);

            $callback($this);
        }
        else
        {
            $this->current_group_uri = '';
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

        $this->found = true;
    }
}