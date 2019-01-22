<?php
declare(strict_types=1);

namespace src\app\http\controllers;

use Throwable;
use LogicException;
use corbomite\twig\TwigEnvironment;
use Psr\Http\Message\ResponseInterface;
use src\app\http\services\RequireLoginService;
use corbomite\http\exceptions\Http404Exception;
use corbomite\user\interfaces\UserApiInterface;
use corbomite\queue\interfaces\QueueApiInterface;

class AdminController
{
    private $userApi;
    private $response;
    private $queueApi;
    private $twigEnvironment;
    private $requireLoginService;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        QueueApiInterface $queueApi,
        TwigEnvironment $twigEnvironment,
        RequireLoginService $requireLoginService
    ) {
        $this->userApi = $userApi;
        $this->response = $response;
        $this->queueApi = $queueApi;
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

        if (! $user = $this->userApi->fetchCurrentUser()) {
            throw new LogicException('Unknown Error');
        }

        if ($user->getExtendedProperty('is_admin') !== 1) {
            throw new Http404Exception();
        }

        $response = $this->response->withHeader('Content-Type', 'text/html');

        $rows = [];

        $queryModel = $this->userApi->makeQueryModel();
        $queryModel->addOrder('email_address', 'asc');
        $queryModel->addWhere('guid', $user->guid(), '!=');

        foreach ($this->userApi->fetchAll($queryModel) as $userModel) {
            $userIsAdmin = $userModel->getExtendedProperty('is_admin') === 1;
            $styledStatus = 'Inactive';

            if ($userIsAdmin) {
                $styledStatus = 'Good';
            }

            $rows[] = [
                'inputValue' => $userModel->guid(),
                'cols' => [
                    'Email' => $userModel->emailAddress(),
                    'Timezone' => $userModel->getExtendedProperty('timezone') ?: date_default_timezone_get(),
                    'Admin' => $userIsAdmin ? 'Yes' : 'No',
                ],
                'colorStyledCols' => [
                    'Admin' => $styledStatus,
                ],
            ];
        }

        $queueRows = [];

        foreach ($this->queueApi->fetchAllBatches() as $batch) {
            $batch->addedAt()->setTimezone(new \DateTimeZone(
                $user->getExtendedProperty('timezone') ?: date_default_timezone_get()
            ));

            $queueRows[] = [
                'inputValue' => 'null',
                'cols' => [
                    'Title' => $batch->title(),
                    'Percent Complete' => $batch->percentComplete(),
                    'Added At' => $batch->addedAt()->format('n/j/Y g:i:s a'),
                ],
            ];
        }

        // $queueApi = $this->queueApi;
        //
        // $batchModel = $queueApi->makeActionQueueBatchModel();
        // $itemModel1 = $queueApi->makeActionQueueItemModel();
        // $itemModel2 = $queueApi->makeActionQueueItemModel();
        //
        // $itemModel1->class(\corbomite\queue\Noop::class);
        //
        // $itemModel2->class(\corbomite\queue\Noop::class);
        // $itemModel2->method('noop');
        //
        // $batchModel->name('test_name');
        // $batchModel->title('Test Name');
        // $batchModel->addItem($itemModel1);
        // $batchModel->addItem($itemModel2);
        //
        // $queueApi->addToQueue($batchModel);

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('StandardPage.twig', [
                'metaTitle' => 'Admin',
                'title' => 'Admin',
                'pageControlButtons' => [
                    [
                        'href' => '/admin/create-user',
                        'content' => 'Create User',
                    ]
                ],
                'includes' => [
                    [
                        'template' => 'forms/TableListForm.twig',
                        'formTitle' => 'Users',
                        'actionParam' => 'adminUserActions',
                        'actions' => [
                            'promote' => 'Promote Selected to Admin',
                            'demote' => 'Demote Selected from Admin',
                            'delete' => 'Delete Selected',
                        ],
                        'table' => [
                            'inputsName' => 'guids[]',
                            'headings' => [
                                'Email',
                                'Timezone',
                                'Admin'
                            ],
                            'rows' => $rows,
                        ],
                    ],

                    [
                        'template' => 'forms/TableListForm.twig',
                        'formTitle' => 'Process in Queue',
                        'actionParam' => 'null',
                        'table' => [
                            'inputsName' => 'null',
                            'headings' => [
                                'Title',
                                'Percent Complete',
                                'Added At'
                            ],
                            'rows' => $queueRows,
                        ],
                    ],
                ],
            ])
        );

        return $response;
    }
}
