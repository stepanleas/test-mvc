<?php

namespace app\repositories\base;

use app\core\Application;

class BaseRepository
{
    protected string $tableName;

    public function __construct($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * Get all the records from the table
     * @param array $fields
     * @return array
     */
    public function getAll($fields = [])
    {
        return Application::$app->db
            ->table($this->tableName)
            ->get($fields);
    }

    /**
     * Count the total number or records
     * from the table
     * @return mixed
     */
    public function count()
    {
        return Application::$app->db
            ->table($this->tableName)
            ->count()[0]['COUNT(*)'];
    }

    /**
     * Paginate the records
     * @param $perPage
     * @param int $pageNo
     * @param array $fields
     * @return mixed
     */
    public function paginate($perPage, $pageNo = 1, $fields = [])
    {
        return Application::$app->db
            ->table($this->tableName)
            ->paginate($perPage, $pageNo)
            ->get($fields);
    }

    /**
     * Find the model by its id
     * @param $id
     * @param array $fields
     * @return mixed
     */
    public function find($id, $fields = [])
    {
        return Application::$app->db
            ->table($this->tableName)
            ->where(['id' => $id])
            ->first($fields);
    }

    /**
     * Delete the record from the database
     * @param $id
     */
    public function delete($id)
    {
        Application::$app->db
            ->table($this->tableName)
            ->where(['id' => $id])
            ->delete();
    }
}