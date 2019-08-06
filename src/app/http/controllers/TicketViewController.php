<?php

declare(strict_types=1);

namespace src\app\http\controllers;

use corbomite\http\exceptions\Http404Exception;
use corbomite\twig\TwigEnvironment;
use corbomite\user\interfaces\UserApiInterface;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use src\app\http\services\RequireLoginService;
use src\app\tickets\interfaces\TicketApiContract;
use Throwable;

class TicketViewController
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

        $guid = $request->getAttribute('guid');

        if (! $guid) {
            throw new Http404Exception();
        }

        try {
            $guidButes = $this->ticketApi->uuidToBytes($guid);
        } catch (Throwable $e) {
            throw new Http404Exception();
        }

        $params = $this->ticketApi->makeQueryModel();
        $params->addWhere('guid', $guidButes);
        $ticket = $this->ticketApi->fetchOne($params);

        if (! $ticket) {
            throw new Http404Exception();
        }

        $status       = $ticket->status();
        $styledStatus = 'Inactive';

        if ($status === 'resolved') {
            $styledStatus = 'Good';
        } elseif ($status === 'on_hold') {
            $styledStatus = 'Error';
        } elseif ($status === 'in_progress') {
            $styledStatus = 'Caution';
        }

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('StandardPage.twig', [
                'tags' => [[
                    'content' => $ticket->humanStatus(),
                    'style' => $styledStatus,
                ],
                ],
                'metaTitle' => $ticket->title(),
                // 'breadCrumbs' => $breadCrumbs,
                'title' => $ticket->title(),
            // 'pageControlButtons' => $pageControlButtons,
                'includes' => [],
            ])
        );

        return $response;
    }
}
