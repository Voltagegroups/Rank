<?php

namespace rank\task;

use pocketmine\scheduler\AsyncTask;
use rank\provider\ProviderBase;

class QuerySqlTask extends AsyncTask
{
    private int $db;
    private int $type;

    public function __construct(int $db, int $type)  {
        $this->db = $db;
        $this->type = $type;
    }

    public function onRun(): void
    {
        $db = $this->db;
        $type = $this->type;
        switch ($db) {
            case ProviderBase::MYSQL_PROVIDER:
                break;
            case ProviderBase::SQLITE_PROVIDER:
                break;
        }
        // TODO: Implement onRun() method.
    }
}