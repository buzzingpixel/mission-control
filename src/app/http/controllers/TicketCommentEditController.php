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

class TicketCommentEditController
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

        $commentGuid = $request->getAttribute('commentGuid');

        if (! $commentGuid) {
            throw new Http404Exception();
        }

        try {
            $commentGuidBytes = $this->ticketApi->uuidToBytes($commentGuid);
        } catch (Throwable $e) {
            throw new Http404Exception();
        }

        $params = $this->ticketApi->makeQueryModel();
        $params->addWhere('guid', $commentGuidBytes);
        $comment = $this->ticketApi->fetchOneThreadItem($params);

        if (! $comment) {
            throw new Http404Exception();
        }

        $hasCommentControl = false;

        if ($user->getExtendedProperty('is_admin') === 1) {
            $hasCommentControl = true;
        } elseif ($comment->user()->guid() === $user->guid()) {
            $hasCommentControl = true;
        }

        if (! $hasCommentControl) {
            $response->getBody()->write(
                $this->twigEnvironment->renderAndMinify(
                    'account/Unauthorized.twig'
                )
            );

            return $response;
        }

        $response->getBody()->write(
            $this->twigEnvironment->renderAndMinify('StandardPage.twig', [
                'metaTitle' => 'Edit Comment',
                'breadCrumbs' => [
                    [
                        'href' => '/tickets',
                        'content' => 'Tickets',
                    ],
                    [
                        'href' => '/tickets/ticket/' . $comment->ticket()->guid(),
                        'content' => $comment->ticket()->title(),
                    ],
                    ['content' => 'Edit Comment'],
                ],
                'title' => 'Edit Comment',
                'includes' => [
                    [
                        'template' => 'forms/StandardForm.twig',
                        'actionParam' => 'editTicketComment',
                        'inputs' => [
                            [
                                'template' => 'Hidden',
                                'name' => 'guid',
                                'value' => $comment->guid(),
                            ],
                            [
                                'template' => 'TextArea',
                                'name' => 'content',
                                'label' => 'Content',
                                'rows' => 16,
                                'value' => $comment->content(),
                            ],
                        ],
                    ],
                ],
            ])
        );

        return $response;
    }
}
