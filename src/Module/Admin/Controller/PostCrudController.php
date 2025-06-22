<?php

namespace App\Module\Admin\Controller;

use App\Framework\Renderer\RendererInterface;
use App\Framework\Router\RouterInterface;
use App\Framework\Service\FlashService;
use App\Framework\Service\ValidationService;
use App\Module\Blog\Repository\CategoryRepository;
use App\Module\Blog\Repository\PostRepository;
use Faker\Factory;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PostCrudController
{

    private $faker;

    public function __construct(
        private RendererInterface $renderer,
        private CategoryRepository $categoryRepository,
        private PostRepository $repository,
        private RouterInterface $router,
        private FlashService $flash,
        private ValidationService $validator
    )
    {
        $this->faker = Factory::create();
    }

    public function index(ServerRequestInterface $request): ResponseInterface{

        $page = (int) ($request->getQueryParams()['page']??1);
        $posts=$this->repository->findPaginated(10,$page);
        return new Response(200,[],$this->renderer->render('@admin/posts/index',compact('posts')));

    }
    public function create(ServerRequestInterface $request): ResponseInterface{

        $errors=[];
        $post=[];
        if($request->getMethod() === 'POST'){

            $post= $request->getParsedBody();

            if(!isset($post['categories'])){
                $post['categories']=[];
            }

            unset($post['_CSRF_INDEX'], $post['_CSRF_TOKEN']);
            $errors=$this->validator->validator($post);


            if(empty($errors)){
                $post['image']="https://picsum.photos/seed/".$this->faker->word()."/280/150";
                $post['created_at']=(new \DateTime())->format('Y-m-d H:i:s');
                $catego=$post['categories'];
                unset($post['categories']);
                $id=$this->repository->insert($post);
                $this->repository->updatePostCategories($id,$catego);

                $this->flash->add('success','Post created successfully');
                return new Response(302, ['Location' => $this->router->generateUri('admin.posts.index')]);
            }

        }


        $categories = $this->categoryRepository->findAll('name asc');
        return new Response(200,[],$this->renderer->render('@admin/posts/create',compact('categories','post','errors')));

    }
    public function edit(ServerRequestInterface $request): ResponseInterface{


        $errors=[];
        $id=(int)($request->getAttribute('id'));
        $post= $this->repository->findByWithCategories($id);

        if($request->getMethod() === 'POST'){

            $post= $request->getParsedBody();
            if(!isset($post['categories'])){
                $post['categories']=[];
            }

            unset($post['_CSRF_INDEX'], $post['_CSRF_TOKEN']);
            $errors=$this->validator->validator($post);


            if(empty($errors)){

                $post['updated_at']=(new \DateTime())->format('Y-m-d H:i:s');
                $catego=$post['categories'];
                unset($post['categories']);
                $this->repository->update($id,$post);
                $this->repository->updatePostCategories($id,$catego);

                $this->flash->add('success','Post updated successfully');
                return new Response(302, ['Location' => $this->router->generateUri('admin.posts.index')]);
            }
        }

        $categories = $this->categoryRepository->findAll('name asc');

        return new Response(200,[],$this->renderer->render('@admin/posts/edit',compact('post','errors','categories')));

    }
    public function delete(ServerRequestInterface $request): ResponseInterface{


        if (!isset($request->getParsedBody()['_CSRF_TOKEN'])) {
            return new Response(400, [], 'Invalid CSRF token');
        }

        $this->repository->delete((int)($request->getAttribute('id')));
        $this->flash->add('success', 'Post has been deleted');


        return new Response(302,['Location' => $this->router->generateUri('admin.posts.index')]);

    }
}
