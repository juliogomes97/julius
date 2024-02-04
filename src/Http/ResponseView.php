<?php declare(strict_types=1);

namespace Julius\Framework\Http;

use \Twig\Loader\FilesystemLoader;
use \Twig\Environment;
use \Twig\Extension\ExtensionInterface;

final class ResponseView
{
    public readonly Environment $twig;

    public function __construct(string $root_views, array $options = [])
    {
        $loader = new FilesystemLoader($root_views);

        $this->twig = new Environment($loader, array_merge([
                'debug' => false,
                'cache' => false
            ], $options
        ));
    }

    public function addGlobalVariable(string $name, mixed $value) : void
    {
        $this->twig->addGlobal($name, $value);
    }

    public function addExtension(ExtensionInterface $extension) : void
    {
        $this->twig->addExtension($extension);
    }

    public function render(string $view, array $parameters = []) : void
    {
        $template = $this->twig->load($view);

        echo $template->render([
            'controller' => $parameters
        ]);
    }
}