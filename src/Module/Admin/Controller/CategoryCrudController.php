<?php

namespace App\Module\Admin\Controller;

use App\Framework\Renderer\RendererInterface;
use App\Framework\Router\RouterInterface;
use App\Framework\Service\FlashService;
use App\Framework\Service\ValidationService;
use App\Module\Blog\Repository\CategoryRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use GuzzleHttp\Psr7\Response;
class CategoryCrudController
{

    public function __construct(
        private RendererInterface $renderer,
        private CategoryRepository $repository,
        private RouterInterface $router,
        private FlashService $flash,
        private ValidationService $validator
    )
    {
    }
    public function index(ServerRequestInterface $request): ResponseInterface{

        $page = (int) ($request->getQueryParams()['page']??1);
        $categories=$this->repository->findPaginated(5,$page);
        return new Response(200,[],$this->renderer->render('@admin/categories/index',compact('categories')));

    }
    public function create(ServerRequestInterface $request): ResponseInterface{


        $errors=[];
        $category=[];
        if($request->getMethod() === 'POST'){

            $category= $request->getParsedBody();


            unset($category['_CSRF_INDEX'], $category['_CSRF_TOKEN']);
            $errors=$this->validator->validator($category);


            if(empty($errors)){

                $this->repository->insert($category);


                $this->flash->add('success','Category created successfully');
                return new Response(302, ['Location' => $this->router->generateUri('admin.categories.index')]);
            }

        }
        return new Response(200,[],$this->renderer->render('@admin/categories/create',compact('errors','category')));

    }
    public function edit(ServerRequestInterface $request): ResponseInterface{


        $errors=[];
        $id=(int)$request->getAttribute('id');
        $category= $this->repository->findBy('id',$id);

        if($request->getMethod() === 'POST'){

            $category= $request->getParsedBody();


            unset($category['_CSRF_INDEX'], $category['_CSRF_TOKEN']);
            $errors=$this->validator->validator($category);


            if(empty($errors)){

                $this->repository->update($id,$category);


                $this->flash->add('success','Category updated successfully');
                return new Response(302, ['Location' => $this->router->generateUri('admin.categories.index')]);
            }

        }
        return new Response(200,[],$this->renderer->render('@admin/categories/edit',compact('errors','category')));

    }
    public function delete(ServerRequestInterface $request): ResponseInterface{


        if (!isset($request->getParsedBody()['_CSRF_TOKEN'])) {
            return new Response(400, [], 'Invalid CSRF token');
        }

        $this->repository->delete((int)($request->getAttribute('id')));
        $this->flash->add('success', 'Category has been deleted');


        return new Response(302,['Location' => $this->router->generateUri('admin.categories.index')]);

    }
}
