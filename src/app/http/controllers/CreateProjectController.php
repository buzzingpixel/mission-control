<?php
declare(strict_types=1);

namespace src\app\http\controllers;

use Throwable;
use corbomite\twig\TwigEnvironment;
use Psr\Http\Message\ResponseInterface;
use src\app\http\services\RequireLoginService;
use corbomite\user\interfaces\UserApiInterface;

class CreateProjectController
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

        if (! $this->userApi->fetchCurrentUser()->userDataItem('admin')) {
            $response->getBody()->write(
                $this->twigEnvironment->renderAndMinify('account/Unauthorized.twig')
            );

            return $response;
        }

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('forms/StandardForm.twig', [
                'breadCrumbs' => [
                    [
                        'href' => '/projects',
                        'content' => 'Projects'
                    ],
                    [
                        'content' => 'Create'
                    ]
                ],
                'actionParam' => 'createProject',
                'formTitle' => 'Create New Project',
                'inputs' => [
                    [
                        'template' => 'Text',
                        'type' => 'text',
                        'name' => 'title',
                        'label' => 'Title',
                    ],

                    [
                        'template' => 'TextArea',
                        'name' => 'description',
                        'label' => 'Description',
                    ]
                ],
            ])
        );

        return $response;
    }
}
