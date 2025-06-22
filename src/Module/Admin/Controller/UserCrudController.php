<?php

namespace App\Module\Admin\Controller;

use App\Framework\Renderer\RendererInterface;
use App\Framework\Router\RouterInterface;
use App\Framework\Service\FlashService;
use App\Module\User\Repository\UserRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use GuzzleHttp\Psr7\Response;

class UserCrudController
{
    public function __construct(
        private UserRepository $repository,
        private RendererInterface $renderer,
        private RouterInterface $router,
        private FlashService $flash
    )
    {
    }

    public function index(ServerRequestInterface $request): ResponseInterface{

        $page = (int) ($request->getQueryParams()['page']??1);
        $users=$this->repository->findPaginated(10,$page);
        return new Response(200,[],$this->renderer->render('@admin/users/index',compact('users')));

    }

    public function edit(ServerRequestInterface $request): ResponseInterface{

        $id=($request->getAttribute('id'));

        $user=$this->repository->findBy('id',$id);




        $this->repository->update($id, ['admin' => $user->isAdmin() ? 0 : 1]);

        return new Response(302,['Location'=>$this->router->generateUri('admin.users.index')]);

    }
    public function delete(ServerRequestInterface $request): ResponseInterface{

        if (!isset($request->getParsedBody()['_CSRF_TOKEN'])) {
            return new Response(400, [], 'Invalid CSRF token');
        }

        $this->repository->delete((int)($request->getAttribute('id')));
        $this->flash->add('success', 'User has been deleted');
        return new Response(302,['Location' => $this->router->generateUri('admin.users.index')]);


    }
}
