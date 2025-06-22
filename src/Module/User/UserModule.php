<?php

namespace App\Module\User;

use App\Framework\Renderer\RendererInterface;
use App\Framework\Router\RouterInterface;
use App\Module\User\Controller\UserController;

class UserModule
{
       public function __construct(
         private  RendererInterface $renderer,
         private  RouterInterface $router
       )
       {
           $this->renderer->addPath(__DIR__.'/templates','user');

           $this->router->get('inscription','/register',[UserController::class,'register']);
           $this->router->post('register','/register',[UserController::class,'register']);
           $this->router->get('connexion','/login',[UserController::class,'login']);
           $this->router->post('login','/login',[UserController::class,'login']);
           $this->router->get('logout','/logout',[UserController::class,'logout']);

           //dd($this->router->getRoutes());
       }
}
