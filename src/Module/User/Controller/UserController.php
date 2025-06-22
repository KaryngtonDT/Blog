<?php

namespace App\Module\User\Controller;

use App\Framework\Renderer\RendererInterface;
use App\Framework\Router\RouterInterface;
use App\Framework\Service\FlashService;
use App\Framework\Service\ValidationService;
use App\Module\User\Repository\UserRepository;
use App\Module\User\Service\AuthServiceInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserController
{

    public function __construct(
       private RendererInterface $renderer,
        private RouterInterface $router,
        private AuthServiceInterface $authService,
        private ValidationService $validationService,
        private FlashService $flashService,
    )
    {
    }

    public function register(ServerRequestInterface $request): ResponseInterface{

        $user=[];
        $errors=[];

        if($request->getMethod()==='POST'){

            $user=$request->getParsedBody();
            unset($user['_CSRF_INDEX'],$user['_CSRF_TOKEN']);
           $errors= $this->validationService->validator($user);

           if(empty($errors)){
             if(  $this->authService->register($user['email'],$user['password'])!==false){

                 $this->flashService->add('success','registration successful');
                 return  new Response(302, ['location'=>$this->router->generateUri('connexion')]);
             }
             $this->flashService->add('error','registration unsuccessful');
           }

        }

        return  new Response(200, [], $this->renderer->render('@user/register',compact('user','errors')));

    }

    public function login(ServerRequestInterface $request): ResponseInterface{

        $user=[];
        $errors=[];
        if($request->getMethod()==='POST'){

            $user=$request->getParsedBody();
            unset($user['_CSRF_INDEX'],$user['_CSRF_TOKEN']);
            $errors= $this->validationService->validator($user);

            if(empty($errors)){
                if(  $this->authService->login($user['email'],$user['password'])!==false){

                    $this->flashService->add('success','login successful');
                    return  new Response(302, ['location'=>$this->router->generateUri('posts.index')]);
                }
                $this->flashService->add('danger','wrong credentials');
            }

        }

        return  new Response(200, [], $this->renderer->render('@user/login',compact('user','errors')));

    }

    public function logout(ServerRequestInterface $request): ResponseInterface{

        $this->authService->logout();

        return  new Response(302, ['Location'=> $this->router->generateUri('login')] );

    }

}
