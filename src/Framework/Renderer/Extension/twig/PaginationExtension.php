<?php

namespace App\Framework\Renderer\Extension\twig;

use App\Framework\Router\RouterInterface;
use Pagerfanta\PagerfantaInterface;
use Pagerfanta\View\TwitterBootstrap5View;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PaginationExtension extends AbstractExtension
{
    public function __construct(private RouterInterface $router)
    {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('paginate', [$this, 'renderPagerfanta'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param PagerfantaInterface<mixed>       $pagerfanta
     * @param string $routeName   The name of the view to render
     * @param array<string, mixed>             $attributes
     */
    public function renderPagerfanta(PagerfantaInterface $pagerfanta, string $routeName , array $attributes = []): string
    {
        return (new TwitterBootstrap5View())->render(
            $pagerfanta,
            function (int $page) use ( $routeName, $attributes) {
                return $this->router->generateUri($routeName, $attributes)."?page=$page";
            },
            [
                'proximity' => 3,
                'prev_text' => '&laquo;',
                'next_text' => '&raquo;',
            ]
        );
    }
}
