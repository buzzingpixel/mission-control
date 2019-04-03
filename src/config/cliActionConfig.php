<?php

declare(strict_types=1);

use src\app\cli\actions\DemoteUserFromAdminAction;
use src\app\cli\actions\DevUtilityAction;
use src\app\cli\actions\PromoteUserToAdminAction;

return [
    'control' => [
        'description' => 'Mission Control Commands',
        'commands' => [
            'promote-user' => [
                'description' => 'Promotes a user to admin',
                'class' => PromoteUserToAdminAction::class,
            ],
            'demote-user' => [
                'description' => 'Demotes a user from admin',
                'class' => DemoteUserFromAdminAction::class,
            ],
            'dev-utility' => [
                'description' => 'Do whatever we need during dev',
                'class' => DevUtilityAction::class,
            ],
        ],
    ],
];
