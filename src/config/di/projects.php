<?php
declare(strict_types=1);

use src\app\projects\ProjectsApi;

return [
    ProjectsApi::class => function () {
        return new ProjectsApi();
    },
];
