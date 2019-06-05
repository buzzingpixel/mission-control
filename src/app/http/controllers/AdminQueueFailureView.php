<?php

declare(strict_types=1);

namespace src\app\http\controllers;

use corbomite\http\exceptions\Http404Exception;
use corbomite\queue\interfaces\QueueApiInterface;
use corbomite\twig\TwigEnvironment;
use corbomite\user\interfaces\UserApiInterface;
use DateTimeZone;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use src\app\http\services\RequireLoginService;
use Throwable;
use function date_default_timezone_get;

class AdminQueueFailureView
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
    public function __invoke(ServerRequestInterface $request) : ResponseInterface
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

        $guid = $request->getAttribute('guid');

        $queryModel = $this->queueApi->makeQueryModel();

        try {
            $queryModel->addWhere('guid', $this->queueApi->uuidToBytes($guid));
        } catch (Throwable $e) {
            throw new Http404Exception('Batch item not found');
        }

        $queryModel->addWhere('finished_due_to_error', 1);

        $model = $this->queueApi->fetchOneBatch($queryModel);

        if (! $model) {
            throw new Http404Exception('Batch item not found');
        }

        $addedAt = $model->addedAt();

        if ($addedAt) {
            $addedAt->setTimezone(new DateTimeZone(
                $user->getExtendedProperty('timezone') ?: date_default_timezone_get()
            ));
        }

        $finishedAt = $model->finishedAt();

        if ($finishedAt) {
            $finishedAt->setTimezone(new DateTimeZone(
                $user->getExtendedProperty('timezone') ?: date_default_timezone_get()
            ));
        }

        $keyValueItems = [
            [
                'key' => 'Title',
                'value' => $model->title(),
            ],
            [
                'key' => 'Percent Complete',
                'value' => $model->percentComplete(),
            ],
            [
                'key' => 'Added At',
                'value' => $addedAt ? $addedAt->format('n/j/Y g:i:s a') : '',
            ],
            [
                'key' => 'Failed At',
                'value' => $finishedAt ? $finishedAt->format('n/j/Y g:i:s a') : '',
            ],
            [
                'key' => 'Error Message',
                'value' => $model->errorMessage(),
                'showInTextArea' => true,
                'textAreaRows' => 25,
            ],
        ];

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('StandardPage.twig', [
                'metaTitle' => 'Viewing Queue Failure',
                'breadCrumbs' => [
                    [
                        'href' => '/admin',
                        'content' => 'Admin',
                    ],
                    [
                        'href' => '/admin/queue-failures',
                        'content' => 'Queue Failures',
                    ],
                    ['content' => 'Viewing Queue Failure'],
                ],
                'title' => 'Viewing Queue Failure',
                'includes' => [
                    [
                        'template' => 'includes/KeyValue.twig',
                        'keyValueItems' => $keyValueItems,
                    ],
                ],
            ])
        );

        return $response;
    }
}
