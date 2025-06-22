<?php

namespace App\Module\Admin\Controller;

use App\Framework\Renderer\RendererInterface;
use App\Module\Blog\BlogModule;
use App\Module\User\UserModule;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use GuzzleHttp\Psr7\Response;

class AdminController
{

    public function __construct(private ContainerInterface $container)
    {
    }

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        $renderer = $this->container->get(RendererInterface::class);
        $entities=[];

        if(in_array(BlogModule::class, $this->container->get('modules'), true)){
            $entities[]='posts';
            $entities[]='categories';
        }
        if(in_array(UserModule::class, $this->container->get('modules'), true)){
            $entities[]='users';

        }


        return new Response(200,[],$renderer->render('@admin/index',compact('entities')));
    }
}
