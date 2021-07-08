<?php

namespace app\repositories;

use app\core\Application;
use app\repositories\base\BaseRepository;

class TaskRepository extends BaseRepository
{
    public function __construct($tableName)
    {
        parent::__construct($tableName);
    }

    /**
     * Get paginated and ordered tasks
     * @param $perPage
     * @param int $pageNo
     * @param string $column
     * @param string $orderType
     * @return array
     */
    public function getPaginatedAndOrderedTasks($perPage, $pageNo = 1, $column = 'id', $orderType = 'ASC')
    {
        return Application::$app->db
            ->table($this->tableName)
            ->orderBy($column, $orderType)
            ->paginate($perPage, $pageNo)
            ->get();
    }
}