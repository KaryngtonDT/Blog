<?php

use App\Framework\App;

use GuzzleHttp\Psr7\ServerRequest;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

require dirname(__DIR__).'/vendor/autoload.php';

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

$whoops = new Run;
$whoops->pushHandler(new PrettyPageHandler);
$whoops->register();

$app= (new App(dirname(__DIR__).'/config/container.php'))



;

(new SapiEmitter())->emit($app->handle(ServerRequest::fromGlobals()));
