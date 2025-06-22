<?php

namespace App\Framework\Router;


use PhpDevCommunity\Route;
use PhpDevCommunity\Router as PhpRouter;
use Psr\Http\Message\ServerRequestInterface;



class Router implements RouterInterface
{

    private $router;
    private ?array $routes=null;


    public function __construct()
    {
        $this->router = new PhpRouter();
    }


    public function get(
        string $name,
        string $path,
         $handler,
        array $wheres=[]

    ):void
    {
         $this->add($name,$path,$handler,['GET'],$wheres);
    }

    public function post(
        string $name,
        string $path,
         $handler,
        array $wheres=[]

    ):void
    {
        $this->add($name,$path,$handler,['POST'],$wheres);
    }

    public function delete(
        string $name,
        string $path,
         $handler,
        array $wheres=[]

    ):void
    {
        $this->add($name,$path,$handler,['DELETE'],$wheres);
    }


    public function crud($prefixName,$prefixPath,$handler){

        $this->get("$prefixName.index",$prefixPath,[$handler,'index']);
        $this->get("$prefixName.new","$prefixPath/new",[$handler,'create']);
        $this->post("$prefixName.create","$prefixPath/new",[$handler,'create']);
        $this->get("$prefixName.edit","$prefixPath/{id}",[$handler,'edit'],
        ['id'=>'[0-9]+']);
        $this->post("$prefixName.update","$prefixPath/{id}",[$handler,'edit'],
        ['id'=>'[0-9]+']);
        $this->delete("$prefixName.delete","$prefixPath/{id}",[$handler,'delete'],
        ['id'=>'[0-9]+']);

    }
    public function match(ServerRequestInterface $serverRequest): ?Route
    {
        try {
            return $this->router->match($serverRequest);
        }catch (\Exception){
            return  null;
        }

    }

    public function generateUri(string $name, array $parameters = []): string
    {
        return $this->router->generateUri($name, $parameters);

    }

    public function has(string $name):bool{
        try {
            $this->generateUri($name);
            return true;

        }catch (\Exception $e ){
            return false;
        }
    }


    public function getRoutes(): ?array
    {
        return $this->routes;
    }


    private  function add(
        string $name,
        string $path,
        $handler,
        array $methods ,
        array $wheres=[]
    ){

        $route = new Route($name, $path, $handler, $methods);
        foreach ($wheres as $key=>$regex) {
            $route->where($key, $regex);
        }

       if( $this->router->add($route)){
           $this->routes[]=[$name,$path,$handler,$methods];
       }

    }



}
