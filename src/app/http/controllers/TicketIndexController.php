<?php

declare(strict_types=1);

namespace src\app\http\controllers;

use corbomite\http\exceptions\Http404Exception;
use corbomite\twig\TwigEnvironment;
use corbomite\user\interfaces\UserApiInterface;
use DateTimeImmutable;
use DateTimeZone;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use src\app\http\services\RequireLoginService;
use src\app\tickets\interfaces\TicketApiContract;
use Throwable;
use function date_default_timezone_get;
use function explode;
use function max;

class TicketIndexController
{
    /** @var RequireLoginService */
    private $requireLogin;
    /** @var UserApiInterface */
    private $userApi;
    /** @var ResponseInterface */
    private $response;
    /** @var TwigEnvironment */
    private $twigEnvironment;
    /** @var TicketApiContract */
    private $ticketApi;

    public function __construct(
        RequireLoginService $requireLogin,
        UserApiInterface $userApi,
        ResponseInterface $response,
        TwigEnvironment $twigEnvironment,
        TicketApiContract $ticketApi
    ) {
        $this->requireLogin    = $requireLogin;
        $this->userApi         = $userApi;
        $this->response        = $response;
        $this->twigEnvironment = $twigEnvironment;
        $this->ticketApi       = $ticketApi;
    }

    /**
     * @throws Throwable
     */
    public function __invoke(ServerRequestInterface $request) : ResponseInterface
    {
        $requireLogin = $this->requireLogin->requireLogin();

        if ($requireLogin) {
            return $requireLogin;
        }

        $user = $this->userApi->fetchCurrentUser();

        if (! $user) {
            throw new LogicException('An unknown error occurred');
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

        $page = $request->getAttribute('page');

        if ($page === '0' || $page === '1') {
            throw new Http404Exception();
        }

        $page   = max(1, (int) $page);
        $limit  = 100;
        $offset = ($limit * $page) - $limit;

        /** @var string[] $body */
        $query = $request->getQueryParams();

        $queryString = '';

        foreach ($query as $key => $value) {
            $queryString .= $queryString ? '&' : '?';
            $queryString .= $key . '=' . $value;
        }

        $status = $query['status'] ?? null;

        $params = $this->ticketApi->makeQueryModel();
        $params->addWhere('status', 'new');
        $params->addOrder('added_at_utc', 'desc');
        $params->limit($limit);
        $params->offset($offset);

        if ($status) {
            $status = explode('|', $status);

            $params->addWhere('status', $status);
        }

        $rows = [];

        foreach ($this->ticketApi->fetchAll($params) as $model) {
            $assignedTo = $model->assignedToUser();

            $created = DateTimeImmutable::createFromFormat(
                $model::DATE_TIME_PRECISION_FORMAT,
                $model->addedAt()->format($model::DATE_TIME_PRECISION_FORMAT)
            );

            $created = $created->setTimezone(new DateTimeZone(
                $user->getExtendedProperty('timezone') ?: date_default_timezone_get()
            ));

            $rows[] = [
                'inputValue' => '',
                'actionButtonLink' => '/tickets/ticket/' . $model->guid(),
                'cols' => [
                    'Title' => $model->title(),
                    'Status' => $model->humanStatus(),
                    'Created By' => $model->createdByUser()->emailAddress(),
                    'Assigned To' => $assignedTo ? $assignedTo->emailAddress() : '',
                    'Date Created' => $created->format('l, F j, Y g:ia'),
                ],
            ];
        }

        $metaTitle = $title = 'Tickets';

        $pageControlButtons = [
            [
                'href' => '/tickets/create',
                'content' => 'Create Ticket',
            ],
        ];

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('StandardPage.twig', [
                'metaTitle' => $metaTitle,
                'title' => $title,
                'pageControlButtons' => $pageControlButtons,
                'includes' => [
                    [
                        'template' => 'forms/TableListForm.twig',
                        // 'actionParam' => 'serverListActions',
                        // 'actions' => $actions,
                        'actionColButtonContent' => 'View&nbsp;Ticket',
                        'table' => [
                            'inputsName' => 'guids[]',
                            'headings' => [
                                'Title',
                                'Status',
                                'Created By',
                                'Assigned To',
                                'Date Created',
                            ],
                            'rows' => $rows,
                        ],
                    ],
                ],
            ])
        );

        return $response;
    }
}
