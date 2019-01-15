<?php
declare(strict_types=1);

namespace src\app\http\controllers;

use Throwable;
use corbomite\twig\TwigEnvironment;
use Psr\Http\Message\ResponseInterface;

class RenderErrorPageController
{
    private $twig;
    private $response;

    public function __construct(
        TwigEnvironment $twig,
        ResponseInterface $response
    ) {
        $this->twig = $twig;
        $this->response = $response;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(int $statusCode)
    {
        $response = $this->response->withStatus($statusCode)
            ->withHeader('Content-Type', 'text/html');

        $response->getBody()->write(
            $this->twig->renderAndMinify('ServerError.twig', [
                'statusCode' => $statusCode,
            ])
        );

        return $response;
    }
}
