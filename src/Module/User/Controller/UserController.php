<?php

namespace App\Module\User\Controller;

use App\Framework\Renderer\RendererInterface;
use App\Framework\Router\RouterInterface;
use App\Module\User\Repository\UserRepository;
use App\Module\User\Service\AuthServiceInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserController
{

    public function __construct(
       private RendererInterface $renderer,
        private UserRepository $userRepository,
        private RouterInterface $router,
        private AuthServiceInterface $authService
    )
    {
    }

    public function register(ServerRequestInterface $request): ResponseInterface{

        $user=[];
        $errors=[];

        return  new Response(200, [], $this->renderer->render('@user/register',compact('user','errors')));

    }

    public function login(ServerRequestInterface $request): ResponseInterface{

        $user=[];
        $errors=[];

        return  new Response(200, [], $this->renderer->render('@user/login',compact('user','errors')));

    }

    public function logout(ServerRequestInterface $request): ResponseInterface{

        $this->authService->logout();

        return  new Response(200, ['Location'=> $this->router->generateUri('')] );

    }

}
