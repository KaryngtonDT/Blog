<?php

namespace App\Module\Blog;

use App\Framework\Renderer\RendererInterface;
use App\Framework\Router\RouterInterface;
use App\Module\Blog\Controller\BlogController;

class BlogModule
{

    public function __construct(
        private  RendererInterface $renderer,
        private  RouterInterface $router
    )
    {
        $this->renderer->addPath(__DIR__ . '/templates', 'blog');

        $this->router->get('posts.index','/posts',[BlogController::class,'index']);

        $this->router->get('posts.show','/posts/{slug}/{id}', [BlogController::class,'show'],[
            'slug' => '[a-z0-9-]+',
            'id'=>'[0-9]+'
        ]);

        $this->router->get('posts.category.index','posts/category/{slug}', [BlogController::class,'index'],[
            'slug'=>'[a-z0-9-]+'
        ]);

    }
}
