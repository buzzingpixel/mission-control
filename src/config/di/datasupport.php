<?php
declare(strict_types=1);

use corbomite\di\Di;
use src\app\datasupport\BuildQuery;
use corbomite\db\Factory as OrmFactory;

return [
    BuildQuery::class => function () {
        return new BuildQuery(new OrmFactory());
    },
];
