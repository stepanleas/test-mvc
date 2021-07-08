<?php

namespace app\core;

abstract class Model
{
    public $created_at;
    public $updated_at;

    /**
     * Set the model properties based
     * on the array keys
     * Model constructor.
     * @param array $data
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }

        // Currently the created_at is also updating
        $date = date('Y-m-d H:i:s');

        $this->created_at = $date;
        $this->updated_at = $date;
    }

    /**
     * Save the model in the database
     * @return false|string|array
     */
    public function save()
    {
        try {
            return Application::$app->db
                ->save($this);
        } catch (\ReflectionException $e) {
            return Response::sendJsonMessage($e->getMessage(), 'danger');
        }
    }
}