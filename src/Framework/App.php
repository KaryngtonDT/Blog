<?php

namespace App\Framework;

use App\Framework\Renderer\RendererInterface;
use App\Framework\Router\RouterInterface;
use App\Module\User\Service\AuthServiceInterface;
use App\Module\User\UserModule;
use DI\ContainerBuilder;
use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class App implements RequestHandlerInterface
{
    private ?ContainerInterface $container=null;
    private array $middlewares=[];
    private int $index=0;
    public function __construct(private string $configPath)
    {
    }

    public function run(ServerRequestInterface $request): ResponseInterface{


        foreach($this->getContainer()->get('modules') as $module){
            $this->getContainer()->get($module);
        }

        $renderer = $this->getContainer()->get(RendererInterface::class);
        $renderer->addGlobal('app', $request);

        if($this->getContainer()->has(UserModule::class)){
            $renderer->addGlobal('auth',AuthServiceInterface::class );
        }

        $router = $this->getContainer()->get(RouterInterface::class);

        $route=$router->match($request);



        if($route !== null){
            foreach ($route->getAttributes() as $key => $value) {
                $request= $request->withAttribute($key, $value);
            }


            $handler = $route->getHandler();
            [$controller,$method] = $handler;

            return  call_user_func([$this->container->get($controller),$method],$request);



        }

        return new Response(404,[],$renderer->render('404'));
    }



    public function getContainer(): ContainerInterface{

        if($this->container === null){
            $builder= new ContainerBuilder();
            $builder->addDefinitions($this->configPath);

            $this->container=$builder->build();
        }

        return $this->container;
    }


    public function pipe(string $middleware):self{
        $this->middlewares[]=$middleware;
        return $this;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {

        static $booted=false;
        if(!$booted){
            foreach($this->getContainer()->get('modules') as $module){
                $this->getContainer()->get($module);
            }
            $booted=true;
        }

        if(isset($this->middlewares[$this->index])){
            ($this->getContainer()->get($this->middlewares[$this->index++]))->process($request,$this);
        }


       return  $this->run($request);
    }
}
