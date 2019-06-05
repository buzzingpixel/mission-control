<?php

declare(strict_types=1);

namespace src\app\http\controllers;

use corbomite\queue\interfaces\QueueApiInterface;
use corbomite\twig\TwigEnvironment;
use corbomite\user\interfaces\UserApiInterface;
use DateTimeZone;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use src\app\http\services\RequireLoginService;
use Throwable;
use function date_default_timezone_get;

class AdminQueueFailures
{
    /** @var UserApiInterface */
    private $userApi;
    /** @var ResponseInterface */
    private $response;
    /** @var QueueApiInterface */
    private $queueApi;
    /** @var TwigEnvironment */
    private $twigEnvironment;
    /** @var RequireLoginService */
    private $requireLoginService;

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        QueueApiInterface $queueApi,
        TwigEnvironment $twigEnvironment,
        RequireLoginService $requireLoginService
    ) {
        $this->response            = $response;
        $this->requireLoginService = $requireLoginService;
        $this->userApi             = $userApi;
        $this->twigEnvironment     = $twigEnvironment;
        $this->queueApi            = $queueApi;
    }

    /**
     * @throws Throwable
     */
    public function __invoke() : ResponseInterface
    {
        $requireLogin = $this->requireLoginService->requireLogin();

        if ($requireLogin) {
            return $requireLogin;
        }

        $user = $this->userApi->fetchCurrentUser();

        if (! $user) {
            throw new LogicException('Unknown Error');
        }

        $response = $this->response->withHeader('Content-Type', 'text/html');

        if ($user->getExtendedProperty('is_admin') !== 1) {
            $response->getBody()->write(
                $this->twigEnvironment->renderAndMinify(
                    'account/Unauthorized.twig'
                )
            );

            return $response;
        }

        $queueRows = [];

        $queryModel = $this->queueApi->makeQueryModel();
        $queryModel->addOrder('finished_at', 'desc');
        $queryModel->addWhere('finished_due_to_error', 1);
        $queryModel->addWhere('assume_dead_after_time_zone', '', '!=');
        $queryModel->limit(8);

        foreach ($this->queueApi->fetchAllBatches($queryModel) as $batch) {
            $addedAt = $batch->addedAt();

            if ($addedAt) {
                $addedAt->setTimezone(new DateTimeZone(
                    $user->getExtendedProperty('timezone') ?: date_default_timezone_get()
                ));
            }

            $finishedAt = $batch->finishedAt();

            if ($finishedAt) {
                $finishedAt->setTimezone(new DateTimeZone(
                    $user->getExtendedProperty('timezone') ?: date_default_timezone_get()
                ));
            }

            $queueRows[] = [
                'inputValue' => 'null',
                'actionButtonLink' => '/admin/queue-failures/' . $batch->guid(),
                'cols' => [
                    'Title' => $batch->title(),
                    'Percent Complete' => $batch->percentComplete(),
                    'Added At' => $addedAt ? $addedAt->format('n/j/Y g:i:s a') : '',
                    'Failed At' => $finishedAt ? $finishedAt->format('n/j/Y g:i:s a') : '',
                ],
            ];
        }

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('StandardPage.twig', [
                'metaTitle' => 'Queue Failures',
                'breadCrumbs' => [
                    [
                        'href' => '/admin',
                        'content' => 'Admin',
                    ],
                    ['content' => 'Queue Failures'],
                ],
                'title' => 'Queue Failures',
                'includes' => [
                    [
                        'template' => 'forms/TableListForm.twig',
                        'actionParam' => 'null',
                        'actionColButtonContent' => 'Details',
                        'table' => [
                            'inputsName' => 'null',
                            'headings' => [
                                'Title',
                                'Percent Complete',
                                'Added At',
                                'Failed At',
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
