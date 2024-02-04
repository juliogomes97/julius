<?php declare(strict_types=1);

namespace Julius\Test\Controllers;

use \Julius\Framework\Controllers\MasterController;
use \Julius\Framework\Http\ResponseView;
use \Julius\Framework\Http\Request;
use \Julius\Framework\Http\HttpHandler;

abstract class Controller extends MasterController
{
    private static HttpHandler $httpHandler;

    public function __construct(Request $request)
    {
        parent::__construct($request, [
            'lifetime' => 3600 // Duração da sessão é de 1 hora
        ]);

        self::$httpHandler = new HttpHandler;
    }

    protected static function statusCode(int $code) : void
    {
        self::$httpHandler->setStatusCode($code);
    }

    protected static function view(string $view, array $parameters = []) : void
    {
        self::$httpHandler->addHeader('Content-Type', 'text/html');
        self::$httpHandler->defineHeaders();

        $templatesPath = dirname(__DIR__) . '/Views';

        $responseView = new ResponseView($templatesPath, [
            // Mudar o atributo debug para false em mode de produção
            'debug' => true,
            // Em mode de desenvolvimento é recomenda o atributo 'cache' ter o valor em false
            // Em pode de produção para menhor desempenho podemos adicionar o caminho para a 'cache'
            'cache' => false // Caminho para a cache => CONST_TWIG_CACHE_PATH
        ]);

        $responseView->addGlobalVariable('global', [
            'hello' => 'world'
        ]);

        $responseView->render($view, $parameters);
    }

    protected static function toJson(array $items = [], int $flags = 0) : void
    {
        self::$httpHandler->addHeader('Content-Type', 'application/json');
        self::$httpHandler->defineHeaders();

        echo json_encode($items, $flags);
    }
}