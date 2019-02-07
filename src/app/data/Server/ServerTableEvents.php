<?php
declare(strict_types=1);

namespace src\app\data\Server;

use Atlas\Query\Delete;
use Atlas\Query\Insert;
use Atlas\Query\Select;
use Atlas\Query\Update;
use Atlas\Table\Row;
use Atlas\Table\Table;
use Atlas\Table\TableEvents;
use PDOStatement;

class ServerTableEvents extends TableEvents
{
}
