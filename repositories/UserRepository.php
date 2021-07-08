<?php

namespace app\repositories;

use app\core\Application;
use app\repositories\base\BaseRepository;

class UserRepository extends BaseRepository
{
    public function __construct($tableName)
    {
        parent::__construct($tableName);
    }

    /**
     * Find the user by his name
     * @param $name
     * @return mixed
     */
    public function findUserByName($name)
    {
        return Application::$app->db
            ->table($this->tableName)
            ->where([
                'name' => $name
            ])
            ->first();
    }
}