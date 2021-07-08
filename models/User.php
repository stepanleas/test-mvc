<?php

namespace app\models;

use app\core\Model;

class User extends Model
{
    public $id;
    public $name;
    public $email;
    public $password;
}