<?php

declare(strict_types=1);

namespace src\app\http\controllers;

use corbomite\twig\TwigEnvironment;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class RenderErrorPageController
{
    /** @var TwigEnvironment */
    private $twig;
    /** @var ResponseInterface */
    private $response;

    public function __construct(
        TwigEnvironment $twig,
        ResponseInterface $response
    ) {
        $this->twig     = $twig;
        $this->response = $response;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(int $statusCode) : ResponseInterface
    {
        $response = $this->response->withStatus($statusCode)
            ->withHeader('Content-Type', 'text/html');

        $response->getBody()->write(
            $this->twig->renderAndMinify('ServerError.twig', ['statusCode' => $statusCode])
        );

        return $response;
    }
}
