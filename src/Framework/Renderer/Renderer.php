<?php

namespace App\Framework\Renderer;

use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Renderer implements RendererInterface
{
    private Environment $twig;
    private FilesystemLoader $loader;
    public function __construct(private ContainerInterface $container)
    {
        $this->loader=new FilesystemLoader($this->container->get('templates'));
        $this->twig= new Environment(
            $this->loader,
            [
                'debug' => true,
                'cache' => false,
            ]
        );

        foreach ($this->container->get('extensions') as $extension) {
            $this->twig->addExtension($extension);
        }
    }

    public function addPath(string $path, string $namespace): void
    {
        $this->loader->addPath($path, $namespace);

    }

    public function addGlobal(string $name, $value)
    {
      $this->twig->addGlobal($name, $value);

    }

    public function render($name, array $context = []): string
    {
        return $this->twig->render($name.'.html.twig', $context);
    }


}
