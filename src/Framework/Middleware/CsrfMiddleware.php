<?php

namespace App\Framework\Middleware;

use GuzzleHttp\Psr7\Response;
use ParagonIE\AntiCSRF\AntiCSRF;
use ParagonIE\AntiCSRF\Exception\AntiCSRFException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CsrfMiddleware implements MiddlewareInterface
{
    private $csrf;
    public function __construct(
    )
    {
        $this->csrf = new AntiCSRF();
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $body = $request->getParsedBody();
        if($body && isset($body['_METHOD']) && strtoupper($body['_METHOD'])==='DELETE') {
            $request = $request->withMethod('DELETE');
        }

        if($request->getMethod() === "POST"){
            try {
                $this->csrf->validateRequest();
            }catch (AntiCSRFException ){
               return  new Response(403,[],'Invalid CSRF token');
            }
        }

      return $handler->handle($request);
    }

    public function getToken():string{
        return $this->csrf->insertToken();

    }
}
