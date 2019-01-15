<?php
declare(strict_types=1);

namespace src\app\http\middlewares;

use Throwable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use corbomite\http\exceptions\Http404Exception;
use src\app\http\controllers\RenderErrorPageController;

class ErrorPagesMiddleware implements MiddlewareInterface
{
    private $renderErrorPage;

    public function __construct(RenderErrorPageController $renderErrorPage)
    {
        $this->renderErrorPage = $renderErrorPage;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        try {
            return $handler->handle($request);
        } catch (Throwable $e) {
            $code = 500;

            if ($e instanceof Http404Exception ||
                $e->getPrevious() instanceof Http404Exception
            ) {
                $code = 404;
            }

            return ($this->renderErrorPage)($code);
        }
    }
}
