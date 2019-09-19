<?php

namespace denis303\user;

use Exception;
use Config\Services;
use CodeIgniter\Entity;

abstract class BaseUserModel extends \App\Components\BaseModel
{

    const FIELD_PREFIX = 'user_';

    protected $table = 'user';

    protected $primaryKey = self::FIELD_PREFIX . 'id';

    protected $allowedFields = [
        self::FIELD_PREFIX . 'name',
        self::FIELD_PREFIX . 'password_hash',
        self::FIELD_PREFIX . 'email',
        self::FIELD_PREFIX . 'created_at',
        self::FIELD_PREFIX . 'updated_at'
    ];

    protected $returnType = Entity::class;

    public static function setPassword($user, string $password)
    {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
    
        static::setField($user, 'password_hash', $password_hash);
    }

    public static function validatePassword($user, string $password) : bool
    {
        $password_hash = static::getField($user, 'password_hash');

        return password_verify($password, $password_hash);
    }

    public static function getField($user, string $field, bool $applyPrefix = true)
    {
        if ($applyPrefix)
        {
            $field = static::FIELD_PREFIX . $field;
        }

        if (is_array($user))
        {
            return $user[$field];
        }
        else
        {
            return $user->$field;
        }
    }

    public static function setField(&$user, string $field, $value, bool $applyPrefix = true)
    {
        if ($applyPrefix)
        {
            $field = static::FIELD_PREFIX . $field;
        }

        if (is_array($user))
        {
            $user[$field] = $value;
        }
        else
        {
            $user->$field = $value;
        }
    }

    public static function getEmail($user)
    {
        return static::getField($user, 'email', true);
    }

    public static function getName($user)
    {
        return static::getField($user, 'name', true);
    }

    public static function findByEmail($email)
    {
        $class = get_called_class();

        $model = new $class;

        return $model->where([static::FIELD_PREFIX . 'email' => $email])->first();
    }

    public static function createUser(array $data, &$error = null)
    {
        $modelClass = get_called_class();

        $model = new $modelClass;

        $class = $model->returnType;

        $user = new $class;

        if (array_key_exists(static::FIELD_PREFIX . 'password', $data))
        {
            static::setPassword($user, $data[static::FIELD_PREFIX . 'password']);

            unset($data[static::FIELD_PREFIX . 'password']);
        }

        foreach($data as $key => $value)
        {
            $user->$key = $value;
        }

        static::beforeCreateUser($user, $data);

        if (!$model->save($user))
        {
            $errors = $model->errors();

            $error = array_shift($errors);

            return false;
        }

        return $user;
    }

    public static function beforeCreateUser($user, array $data)
    {
    }    

}