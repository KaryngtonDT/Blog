<?php

namespace App\Module\Blog\Controller;

use App\Framework\Renderer\RendererInterface;
use App\Framework\Router\RouterInterface;
use App\Module\Blog\Repository\CategoryRepository;
use App\Module\Blog\Repository\PostRepository;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class BlogController
{

    public function __construct(
        private RendererInterface $renderer,
        private RouterInterface $router,
        private CategoryRepository $categoryRepository,
        private PostRepository $postRepository,
    )
    {
    }

    public function index(ServerRequestInterface $request): ResponseInterface
    {


        $page=(int)($request->getQueryParams()['page']??1);
        $slug=$request->getAttribute('slug');

        $category = $this->categoryRepository->findBy('slug', $slug);


        if($category && $slug) {
            $posts = $this->postRepository->findPaginatedForCategory($category->getId(), 4, $page);

        }
        else{
            $posts = $this->postRepository->findPaginated(4,$page);
        }

        $categories = $this->categoryRepository->findAll();



        return new Response(200,[],$this->renderer->render('@blog/index',compact('posts','categories','category')));

    }
    public function show(ServerRequestInterface $request): ResponseInterface{
        $id=(int)$request->getAttribute('id');
        $slug=$request->getAttribute('slug');
        $post=$this->postRepository->findByWithCategories($id);

        if(!$post){
            return new Response(404,[],$this->renderer->render('404'));
        }
        if ($post->getSlug() !== $slug){
            return new Response(302,[],['location'=>'/posts/'.$post->getSlug().'/'.$id]);
        }


        return new Response(200,[],$this->renderer->render('@blog/show',compact('post')));
    }

}
