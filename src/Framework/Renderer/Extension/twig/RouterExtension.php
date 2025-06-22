<?php
namespace App\Framework\Renderer\Extension\twig;

use App\Framework\Router\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RouterExtension extends AbstractExtension
{
    public function __construct(
        private RouterInterface $router,


    )
    {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('path', [$this, 'generatePath']),
            new TwigFunction('route_exists', [$this, 'routeExists']),
        ];
    }

    public function generatePath(string $path,?array $attributes=[]): string{


        return $this->router->generateUri($path,$attributes);
    }
    public function routeExists(string $name){
        return $this->router->has($name);
    }

}
