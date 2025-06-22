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

    /**
     * Dependencies container (DI)
     * @var ContainerInterface|null
     */
    private ?ContainerInterface $container = null;

    /**
     * list of middlewares to execute
     * @var string[]
     */
    private array $middlewares = [];

    /**
     * current index of the middlewares stack
     * @var int
     */
    private int $index = 0;


    /**
     * @param string $containerConfigPath  path to the dependencies configuration file
     * @param array $modules list of the application modules
     */
    public function __construct(private readonly string $containerConfigPath)
    {
    }


    /**
     * start the application and return a HTTP-response
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     *
     */
    public function run(ServerRequestInterface $request):ResponseInterface{

        //load modules

        foreach ($this->getContainer()->get('modules') as $module) {
            $this->getContainer()->get($module);
        }

        $renderer = $this->getContainer()->get(RendererInterface::class);

        $renderer->addGlobal('app', compact('request'));

        if($this->getContainer()->has(AuthServiceInterface::class)){
            $renderer->addGlobal('auth',$this->getContainer()->get(AuthServiceInterface::class));
        }


        $router= $this->getContainer()->get(RouterInterface::class);


        $route= $router->match($request);
        // dd($router,$request,$router->match($request),$route,is_null($route));
        if(is_null($route)){
            return new Response(404,[],$renderer->render('404'));
        }

        foreach ($route->getAttributes() as $key => $value) {
            $request= $request->withAttribute($key, $value);
        }

        $handler = $route->getHandler();

        [$controller, $method] = $handler;


        return ($this->getContainer()->get($controller))->$method($request);


    }

    /**
     *  Build and configure the app container
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface{

        if($this->container===null){
            $this->container= (new ContainerBuilder())->addDefinitions($this->containerConfigPath)->build();
        }
        return $this->container;
    }


    /**
     * add a middleware to the stack
     * @param string $middleware
     * @return $this
     */
    public function pipe(string $middleware):self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    /**
     * apply middleware and execute run()
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     *
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // make sure all modules are loaded

        static $booted = false;
        if(!$booted){
            foreach ($this->getContainer()->get('modules') as $module) {
                $this->getContainer()->get($module);
            }
            $booted = true;
        }


        if(isset($this->middlewares[$this->index])){


            return  ($this->getContainer()->get($this->middlewares[$this->index++]))->process($request, $this);
        }

        return $this->run($request);
    }
}
