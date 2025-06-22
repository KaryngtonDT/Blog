<?php
namespace App\Framework\Renderer\Extension\twig;

use App\Framework\Router\RouterInterface;
use App\Framework\Service\FlashServiceInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FlashExtension extends AbstractExtension
{
    public function __construct(
        private FlashServiceInterface $flashService,

    )
    {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('flash', [$this->flashService, 'get']),
        ];
    }



}
