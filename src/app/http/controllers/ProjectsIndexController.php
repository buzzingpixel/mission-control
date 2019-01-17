<?php
declare(strict_types=1);

namespace src\app\http\controllers;

use Throwable;
use corbomite\twig\TwigEnvironment;
use Psr\Http\Message\ResponseInterface;
use src\app\http\services\RequireLoginService;

class ProjectsIndexController
{
    private $response;
    private $twigEnvironment;
    private $requireLoginService;

    public function __construct(
        ResponseInterface $response,
        TwigEnvironment $twigEnvironment,
        RequireLoginService $requireLoginService
    ) {
        $this->response = $response;
        $this->twigEnvironment = $twigEnvironment;
        $this->requireLoginService = $requireLoginService;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(): ResponseInterface
    {
        if ($requireLogin = $this->requireLoginService->requireLogin()) {
            return $requireLogin;
        }

        $response = $this->response->withHeader('Content-Type', 'text/html');

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('forms/TableListForm.twig', [
                'actionParam' => 'projectListActions',
                'title' => 'Projects',
                'actions' => [
                    'delete' => 'Delete Selected',
                ],
                'table' => [
                    'inputsName' => 'projects[]',
                    'headings' => [
                        'Title',
                        'Slug',
                        'Description',
                        'Added'
                    ],
                    'rows' => [
                        [
                            'inputValue' => '123',
                            'cols' => [
                                'Title' => 'Test',
                                'Slug' => 'Slug Test',
                                'Description' => 'Description Test',
                                'Added' => '1/2/18 2018',
                            ],
                        ],
                        [
                            'inputValue' => '456',
                            'cols' => [
                                'Title' => 'Test',
                                'Slug' => 'Slug Test',
                                'Description' => 'Description Test',
                                'Added' => '1/2/18 2018',
                            ],
                        ],
                        [
                            'inputValue' => '456',
                            'cols' => [
                                'Title' => 'Test',
                                'Slug' => 'Slug Test',
                                'Description' => 'Description Test',
                                'Added' => '1/2/18 2018',
                            ],
                        ],
                        [
                            'inputValue' => '456',
                            'cols' => [
                                'Title' => 'Test',
                                'Slug' => 'Slug Test',
                                'Description' => 'Description Test',
                                'Added' => '1/2/18 2018',
                            ],
                        ],
                        [
                            'inputValue' => '456',
                            'cols' => [
                                'Title' => 'Test',
                                'Slug' => 'Slug Test',
                                'Description' => 'Description Test',
                                'Added' => '1/2/18 2018',
                            ],
                        ],
                    ],
                ],
            ])
        );

        return $response;
    }
}
