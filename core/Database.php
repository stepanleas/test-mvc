<?php

namespace app\core;
use PDO;

require_once('./config/db.php');

class Database
{
    private $con;
    private $query;
    private $values;

    public function __construct()
    {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';';
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $this->con = new PDO($dsn, DB_USER, DB_PASS, $opt);
    }

    /**
     * Select the table to query on
     * @param $tableName
     * @return $this
     */
    public function table($tableName)
    {
        $this->query = "SELECT * FROM $tableName";
        return $this;
    }

    /**
     * Get the first row from the query
     * @param array $fields
     * @return mixed
     */
    public function first($fields = [])
    {
        if (!empty($fields)) {
            $this->query = $this->changeSelectStatementFields($fields);
        }

        // Limit to one row
        $this->query .= ' LIMIT 1';

        return $this->executeQuery('fetch');
    }

    /**
     * Order the records by providing the
     * column name and the type
     * @param $column
     * @param $type
     * @return Database
     */
    public function orderBy($column, $type = 'ASC')
    {
        if (is_array($column)) {
            $this->query .= " ORDER BY ";
            foreach ($column as $value) {
                $this->query .= "$value $type,";
            }
            $this->query = rtrim($this->query, ',');
        } else {
            $this->query .= " ORDER BY $column $type";
        }

        return $this;
    }

    /**
     * Get an array of table records
     * @param array $fields
     * @return array
     */
    public function get($fields = [])
    {
        if (!empty($fields)) {
            $this->query = $this->changeSelectStatementFields($fields);
        }

        return $this->executeQuery();
    }

    /**
     * Count the total number of records
     * from the table
     * @return mixed
     */
    public function count()
    {
        $this->query = $this->changeSelectStatementFields(['COUNT(*)']);
        return $this->executeQuery();
    }

    /**
     * Paginate the records
     * @param $perPage
     * @param int $pageNo
     * @return mixed
     */
    public function paginate($perPage, $pageNo = 0)
    {
        $offset = ($pageNo - 1) * $perPage;
        $this->query .= " LIMIT $offset, $perPage";
        return $this;
    }

    /**
     * Set the WHERE parameters to the
     * query string
     * @param array $condition
     * @return $this
     */
    public function where($condition = [])
    {
        $keys = implode('=? AND ', array_keys($condition)) . '=?';
        $this->query .= " WHERE $keys";
        $this->values = array_values($condition);

        return $this;
    }

    /**
     * Change the initial SELECT statement
     * from the query string
     * @param $fields
     * @return string|string[]
     */
    private function changeSelectStatementFields($fields)
    {
        $getFields = implode(',', $fields);
        return str_replace('SELECT * FROM', "SELECT $getFields FROM", $this->query);
    }

    /**
     * Execute the query to the database
     * @param string $fetchType
     * @return mixed
     */
    private function executeQuery($fetchType = 'fetchAll')
    {
        try {
            $stmt = $this
                ->con
                ->prepare($this->query);

            $stmt->execute($this->values);
        } catch (\Exception $e) {
            return Response::sendJsonMessage($e->getMessage(), 'danger');
        }

        return $stmt->{$fetchType}(PDO::FETCH_ASSOC);
    }

    /**
     * Create or update the model
     * @param Model $model
     * @return array
     * @throws \ReflectionException
     */
    public function save(Model $model)
    {
        // Get the original class name and lowercase it, thus forming the table name
        $tableName = strtolower((new \ReflectionClass($model))->getShortName() . 's');

        // Convert the model object to an array
        $modelData = (array)$model;

        // Verify if the id is present
        $modelId = false;
        if (is_null($modelData['id']) || isset($modelData['id'])) {
            $modelId = $modelData['id'];
            unset($modelData['id']);
        }

        // The fields that will be updated or inserted
        $queryKeys = array_keys($modelData);

        // Bind the parameters for execution
        $bindParams = [];
        foreach ($modelData as $key => $value) {
            $bindParams[":$key"] = $value;
        }



        /**
         * If the id is present, then it's an update statement,
         * otherwise is an insert statement
         */
        if ($modelId) {
            $bindParams[":id"] = $modelId;
            // Create the update query
            $query = "UPDATE $tableName SET ";
            foreach ($queryKeys as $key) {
                $query .= "$key=:$key, ";
            }

            $query = rtrim($query, ', ');
            $query .= ' WHERE id=:id';
        } else {
            $searchFields = implode(',', $queryKeys);
            $bindValues = ':' . implode(',:', $queryKeys);
            $query = "INSERT INTO $tableName ($searchFields) VALUES ($bindValues)";
        }

        // Prepare the query statement
        $stmt = $this
            ->con
            ->prepare($query);

        $stmt->execute($bindParams);

        return $modelData;
    }

    /**
     * Delete the record from the database
     */
    public function delete()
    {
        $this->query = str_replace('SELECT * FROM', 'DELETE FROM', $this->query);
        $this->executeQuery();
    }
}