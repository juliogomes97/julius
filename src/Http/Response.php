<?php declare(strict_types=1);

namespace Julius\Framework\Http;

use \Twig\Loader\FilesystemLoader;
use \Twig\Environment;
use \Twig\Extension\ExtensionInterface;

class Response
{
    /**
     * Instacia Twig
     *
     * @var Environment
     */
    public readonly Environment $twig;

    /**
     * Itens para Json
     *
     * @var array
     */
    private array $items;

    /**
     * Response constructor.
     *
     * @param string $root_views Caminho para a pasta onde contem os ficheiros .html, .twig e outros
     * @param array $options Opções do framework Twig - Environment
     * 
     */
    public function __construct(string $root_views, array $options = [])
    {
        $loader = new FilesystemLoader($root_views);

        $this->twig = new Environment($loader, array_merge([
                'debug' => false,
                'cache' => false
            ], $options
        ));

        $this->items = [];
    }

    /**
     * Adiciona variaveis globais ao template twig
     *
     * @param string $name Nome da variavel global
     * @param mixed $value Valor da variavel global
     * 
     * @return void
     */
    public function addGlobalVariable(string $name, mixed $value) : void
    {
        $this->twig->addGlobal($name, $value);
    }

    /**
     * Adiciona variaveis globais ao template twig
     *
     * @param ExtensionInterface $extension Extensao para framwork Twig
     * 
     * @return void
     */
    public function addExtension(ExtensionInterface $extension) : void
    {
        $this->twig->addExtension($extension);
    }

    /**
     * Renderizar a view
     *
     * @param string $view Nome do template
     * @param array $parameters Variaveis para o template
     * 
     * @return void
     */
    public function render(string $view, array $parameters = []) : void
    {           
        $template = $this->twig->load($view);

        echo $template->render([
            'controller' => $parameters
        ]);
    }

    /**
     * Adicionar itens para json
     *
     * @param string $key Nome da chave
     * @param mixed $value Dado
     * 
     * @return void
     */
    public function add(string $key, mixed $value) : void
    {
        $this->items[$key] = $value;
    }

    /**
     * Limpar dados do json
     * 
     * @return void
     */
    public function clean() : void
    {
        $this->items = [];
    }

    /**
     * Apresenta os dados em json
     * 
     * @return void
     */
    public function toJson() : void
    {
        echo json_encode($this->items);
    }
}