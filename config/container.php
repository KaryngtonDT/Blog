<?php


use App\Framework\Renderer\Extension\twig\CsrfExtension;
use App\Framework\Renderer\Extension\twig\FlashExtension;
use App\Framework\Renderer\Extension\twig\RouterExtension;
use App\Framework\Renderer\Renderer;
use App\Framework\Renderer\RendererInterface;
use App\Framework\Repository\Repository;
use App\Framework\Repository\RepositoryInterface;
use App\Framework\Router\Router;
use App\Framework\Router\RouterInterface;
use App\Framework\Service\FlashService;
use App\Framework\Service\FlashServiceInterface;
use App\Module\User\Service\AuthService;
use App\Module\User\Service\AuthServiceInterface;
use App\Module\User\UserModule;
use Envms\FluentPDO\Query;
use function DI\autowire;
use function DI\get;

return[
    'modules'=>[
     UserModule::class,
    ],
    UserModule::class=>autowire(UserModule::class),


    'middlewares'=>[

    ],

    'templates'=> dirname(__DIR__).'/src/templates',

    'extensions'=>[
      get(RouterExtension::class),
        get(FlashExtension::class),
        get(CsrfExtension::class),
    ],
     RouterExtension::class=>autowire(RouterExtension::class),
     FlashExtension::class=>autowire(FlashExtension::class),
     CsrfExtension::class=>autowire(CsrfExtension::class),


    PDO::class=> static function () {
        return new PDO(
            "mysql:host=localhost;dbname=blogtest",
            "root",
            "root",
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
      }
    ,
    Query::class=> static function (PDO $pdo) {
    return new Query($pdo);
    },



   RendererInterface::class => autowire(Renderer::class),


    AuthServiceInterface::class => autowire(AuthService::class),
    RouterInterface::class=>autowire(Router::class),
    RepositoryInterface::class=>autowire(Repository::class),
    FlashServiceInterface::class=>autowire(FlashService::class),

];
