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
use src\app\common\Pagination;
use src\app\http\services\RequireLoginService;
use src\app\tickets\interfaces\TicketApiContract;
use Throwable;
use function array_values;
use function date_default_timezone_get;
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

        $page = $request->getAttribute('page');

        if ($page === '0' || $page === '1') {
            throw new Http404Exception();
        }

        $page   = max(1, (int) $page);
        $limit  = 30;
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
        $params->addOrder('added_at_utc', 'desc');
        $params->limit($limit);
        $params->offset($offset);

        $pageControlButtons = [
            'notResolved' => [
                'href' => '/tickets',
                'content' => 'Status: Not Resolved',
            ],
            'new' => [
                'href' => '/tickets?status=new',
                'content' => 'Status: New',
            ],
            'in_progress' => [
                'href' => '/tickets?status=in_progress',
                'content' => 'Status: In Progress',
            ],
            'on_hold' => [
                'href' => '/tickets?status=on_hold',
                'content' => 'Status: On Hold',
            ],
            'resolved' => [
                'href' => '/tickets?status=resolved',
                'content' => 'Status: Resolved',
            ],
            'createTicket' => [
                'href' => '/tickets/create',
                'content' => 'Create Ticket',
            ],
        ];

        $tagStyle = 'Inactive';

        if ($status) {
            $statusTag = $pageControlButtons[$status]['content'];
            unset($pageControlButtons[$status]);
            $params->addWhere('status', $status);

            if ($status === 'resolved') {
                $tagStyle = 'Good';
            } elseif ($status === 'on_hold') {
                $tagStyle = 'Error';
            } elseif ($status === 'in_progress') {
                $tagStyle = 'Caution';
            }
        } else {
            $statusTag = $pageControlButtons['notResolved']['content'];
            unset($pageControlButtons['notResolved']);
            $params->addWhere('status', 'resolved', '!=');
        }

        $rows = [];

        $tickets = $this->ticketApi->fetchAll($params);

        if ($page > 1 && ! $tickets) {
            throw new Http404Exception();
        }

        foreach ($tickets as $model) {
            $assignedTo = $model->assignedToUser();

            $created = DateTimeImmutable::createFromFormat(
                $model::DATE_TIME_PRECISION_FORMAT,
                $model->addedAt()->format($model::DATE_TIME_PRECISION_FORMAT)
            );

            $created = $created->setTimezone(new DateTimeZone(
                $user->getExtendedProperty('timezone') ?: date_default_timezone_get()
            ));

            $status       = $model->status();
            $styledStatus = 'Inactive';

            if ($status === 'resolved') {
                $styledStatus = 'Good';
            } elseif ($status === 'on_hold') {
                $styledStatus = 'Error';
            } elseif ($status === 'in_progress') {
                $styledStatus = 'Caution';
            }

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
                'colorStyledCols' => ['Status' => $styledStatus],
            ];
        }

        $params->limit(0);
        $params->offset(0);

        $metaTitle = $title = 'Tickets';

        $includes = [
            [
                'template' => 'forms/TableListForm.twig',
                'includeFilter' => false,
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
        ];

        $pagination = (new Pagination())->withCurrentPage($page)
            ->withPerPage($limit)
            ->withTotalResults($this->ticketApi->countAll($params))
            ->withBase('/tickets')
            ->withQueryString($queryString);

        if ($pagination->totalPages() > 1) {
            $includes[] = [
                'template' => 'components/Pagination.twig',
                'pagination' => $pagination,
            ];
        }

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('StandardPage.twig', [
                'tags' => [
                    [
                        'content' => $statusTag,
                        'style' => $tagStyle,
                    ],
                ],
                'metaTitle' => $metaTitle,
                'title' => $title,
                'pageControlButtons' => array_values($pageControlButtons),
                'includes' => $includes,
            ])
        );

        return $response;
    }
}
