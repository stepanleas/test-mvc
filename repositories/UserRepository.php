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
     * @param array $fields
     * @return mixed
     */
    public function findUserByName($name, $fields = [])
    {
        return Application::$app->db
            ->table($this->tableName)
            ->where([
                'name' => $name
            ])
            ->first($fields);
    }
}