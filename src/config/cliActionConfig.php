<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

use src\app\cli\actions\PromoteUserToAdminAction;

return [
    'control' => [
        'description' => 'Mission Control Commands',
        'commands' => [
            'promote-user' => [
                'description' => 'Promotes a user to admin',
                'class' => PromoteUserToAdminAction::class,
            ],
        ],
    ],
];
