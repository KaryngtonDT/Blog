<?php

namespace App\Framework\Middleware;

use App\Framework\Renderer\RendererInterface;

use App\Module\User\Service\AuthServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Server\RequestHandlerInterface;

class AdminMiddleware implements MiddlewareInterface
{
    public function __construct(private AuthServiceInterface $authService,private RendererInterface $renderer)
    {

    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uri = $request->getUri()->getPath();
        if(str_starts_with($uri, "/admin") && !$this->authService->isAdmin()){
            return new Response(403,[],$this->renderer->render("403"));
        }


        return $handler->handle($request);
    }
}
