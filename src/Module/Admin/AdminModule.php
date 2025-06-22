<?php

namespace App\Module\Admin;

use App\Framework\Renderer\RendererInterface;
use App\Framework\Router\RouterInterface;
use App\Module\Admin\Controller\AdminController;
use App\Module\Admin\Controller\CategoryCrudController;
use App\Module\Admin\Controller\PostCrudController;
use App\Module\Admin\Controller\UserCrudController;
use Psr\Container\ContainerInterface;

class AdminModule
{
    public function __construct(
        private ContainerInterface $container
    )
    {

        $renderer = $this->container->get(RendererInterface::class);
        $renderer->addPath(__DIR__ . '/templates', 'admin');

        $router = $this->container->get(RouterInterface::class);

        $router->get( 'admin.index',"/admin", [AdminController::class, 'index']);
        $router->crud('admin.posts',"/admin/posts",  PostCrudController::class,);
        $router->crud('admin.categories',"/admin/categories",  CategoryCrudController::class);
        $router->crud('admin.users',"/admin/users",UserCrudController::class);

    }
}
