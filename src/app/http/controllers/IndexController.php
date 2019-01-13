<?php
declare(strict_types=1);

namespace src\app\http\controllers;

use corbomite\twig\TwigEnvironment;
use Psr\Http\Message\ResponseInterface;

class IndexController
{
    private $response;
    private $twigEnvironment;

    public function __construct(
        ResponseInterface $response,
        TwigEnvironment $twigEnvironment
    ) {
        $this->response = $response;
        $this->twigEnvironment = $twigEnvironment;
    }

    public function __invoke(): ResponseInterface
    {
        $response = $this->response->withHeader('Content-Type', 'text/html');

        $response->getBody()->write(
            $this->twigEnvironment->render('Index.twig', [
                'someVar' => 'someVal',
            ])
        );

        return $response;
    }
}
