<?php

namespace app\models;

use app\core\Model;

class Task extends Model
{
    public $id;
    public $user_name;
    public $description;
    public $user_email;
    public $completed = null;
    public $admin_edited = null;
}