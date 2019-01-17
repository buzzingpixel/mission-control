<?php
declare(strict_types=1);

namespace src\app\http\controllers;

use Throwable;
use corbomite\twig\TwigEnvironment;
use Psr\Http\Message\ResponseInterface;
use corbomite\user\interfaces\UserApiInterface;
use src\app\http\services\RequireLoginService;

class ProjectsIndexController
{
    private $userApi;
    private $response;
    private $twigEnvironment;
    private $requireLoginService;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        TwigEnvironment $twigEnvironment,
        RequireLoginService $requireLoginService
    ) {
        $this->userApi = $userApi;
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

        $tableControlButtons = [];

        if ($this->userApi->fetchCurrentUser()->userDataItem('admin')) {
            $tableControlButtons[] = [
                'href' => '/projects/create',
                'content' => 'Create Project'
            ];
        }

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('forms/TableListForm.twig', [
                'actionParam' => 'projectListActions',
                'title' => 'Projects',
                'tableControlButtons' => $tableControlButtons,
                'actions' => [
                    'archive' => 'Archive Selected',
                    'delete' => 'Delete Selected',
                ],
                'actionColButtonContent' => 'View Project',
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
                            'actionButtonLink' => '/thing',
                            'cols' => [
                                'Title' => 'Test',
                                'Slug' => 'Slug Test',
                                'Description' => 'Description Test',
                                'Added' => '1/2/18 2018',
                            ],
                        ],
                        [
                            'inputValue' => '456',
                            'actionButtonLink' => '/asdf',
                            'cols' => [
                                'Title' => 'Test',
                                'Slug' => 'Slug Test',
                                'Description' => 'Description Test',
                                'Added' => '1/2/18 2018',
                            ],
                        ],
                        [
                            'inputValue' => '456',
                            'actionButtonLink' => '/asdf',
                            'cols' => [
                                'Title' => 'Test',
                                'Slug' => 'Slug Test',
                                'Description' => 'Description Test',
                                'Added' => '1/2/18 2018',
                            ],
                        ],
                        [
                            'inputValue' => '456',
                            'actionButtonLink' => '/asdf',
                            'cols' => [
                                'Title' => 'Test',
                                'Slug' => 'Slug Test',
                                'Description' => 'Description Test',
                                'Added' => '1/2/18 2018',
                            ],
                        ],
                        [
                            'inputValue' => '456',
                            'actionButtonLink' => '/asdf',
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
