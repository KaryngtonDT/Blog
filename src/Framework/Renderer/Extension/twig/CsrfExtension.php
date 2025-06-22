<?php
namespace App\Framework\Renderer\Extension\twig;

use App\Framework\Middleware\CsrfMiddleware;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CsrfExtension extends AbstractExtension
{
    public function __construct(
        private CsrfMiddleware $middleware,

    )
    {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('csrf_input', [$this->middleware, 'getToken'],['is_safe' => ['html']]),
        ];
    }



}
