<?php

declare(strict_types=1);

/**
 * @see https://api.slack.com/docs/message-attachments
 */

namespace src\app\notifications\notificationadapters;

use GuzzleHttp\RequestOptions;
use src\app\notifications\interfaces\SendNotificationAdapterInterface;
use src\app\support\extensions\GuzzleClientNoHttpErrors;
use function time;

class SlackNotificationAdapter implements SendNotificationAdapterInterface
{
    /** @var GuzzleClientNoHttpErrors */
    private $guzzleClient;
    /** @var string|null */
    private $slackWebookUrl;

    public function __construct(
        GuzzleClientNoHttpErrors $guzzleClient,
        ?string $slackWebookUrl
    ) {
        $this->guzzleClient   = $guzzleClient;
        $this->slackWebookUrl = $slackWebookUrl;
    }

    /**
     * @param mixed[] $context
     */
    public function send(string $subject, string $message, array $context = []) : void
    {
        if (! $this->slackWebookUrl) {
            return;
        }

        $plainText = $subject . "\n\n" . $message;

        $attachmentColor = '#1a7fba';

        if (isset($context['status'])) {
            if ($context['status'] === 'good') {
                $attachmentColor = '#3c763d';
            } elseif ($context['status'] === 'bad') {
                $attachmentColor = '#a94442';
            }
        }

        $actions = [];

        if (isset($context['urls'])) {
            foreach ($context['urls'] as $url) {
                if (! isset($url['content'], $url['href'])) {
                    continue;
                }

                $actions[] = [
                    'type' => 'button',
                    'text' => $url['content'],
                    'url' => $url['href'],
                ];
            }
        }

        $this->guzzleClient->post($this->slackWebookUrl, [
            RequestOptions::JSON => [
                'attachments' => [
                    [
                        'fallback' => $plainText,
                        'color' => $attachmentColor,
                        'pretext' => $subject,
                        // 'author_name' => 'Mission Control',
                        // 'author_link' => getenv('SITE_URL'),
                        // 'author_icon' => '',
                        // 'title' => $subject,
                        // 'title_link' => '',
                        'text' => $message,
                        'actions' => $actions,
                        // 'fields' => [
                        //     [
                        //         'title' => 'Priority',
                        //         'value' => 'High',
                        //         'short' => false,
                        //     ],
                        // ],
                        // 'image_url' => '',
                        // 'thumb_url' => '',
                        // 'footer' => '',
                        // 'footer_icon' => '',
                        'ts' => time(),
                    ],
                ],
            ],
        ]);
    }
}
