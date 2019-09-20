<?php

namespace denis303\user;

use Exception;
use CodeIgniter\Entity;

abstract class UserModel extends BaseUserModel
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

    protected $createdField = self::FIELD_PREFIX . 'created_at';

    protected $updatedField = self::FIELD_PREFIX . 'updated_at';

    protected $dateFormat = 'datetime';

    public static function saveUser($user, &$error)
    {
        $class = get_called_class();

        $model = new $class;

        return $model->saveUnprotected($user, $error);
    }

    public static function findByEmail($email)
    {
        $class = get_called_class();

        $model = new $class;

        return $model->where([$model::FIELD_PREFIX . 'email' => $email])->first();
    }

    public static function setUserPassword($user, string $password)
    {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
    
        static::setUserField($user, 'password_hash', $password_hash);
    }

    public static function validateUserPassword($user, string $password) : bool
    {
        $password_hash = static::getUserField($user, 'password_hash');

        return password_verify($password, $password_hash);
    }

    public static function getUserField($user, string $field, bool $applyPrefix = true)
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

    public static function setUserField($user, string $field, $value, bool $applyPrefix = true)
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

    public static function getUserEmail($user)
    {
        return static::getUserField($user, 'email', true);
    }

    public static function getUserName($user)
    {
        return static::getUserField($user, 'name', true);
    }

   public static function createUser(array $data, &$error = null)
    {
        $modelClass = get_called_class();

        $model = new $modelClass;

        $class = $model->returnType;

        $user = new $class;

        if (array_key_exists($model::FIELD_PREFIX . 'password', $data))
        {
            static::setUserPassword($user, $data[$model::FIELD_PREFIX . 'password']);

            unset($data[$model::FIELD_PREFIX . 'password']);
        }

        foreach($data as $key => $value)
        {
            $user->$key = trim(strip_tags($value));
        }

        $model->beforeCreateUser($user, $data);

        if (!$model->save($user))
        {
            $errors = $model->errors();

            $error = array_shift($errors);

            return false;
        }

        $id = $model->getInsertID();

        $model = new $modelClass;

        return $model->find($id);
    }

    public function beforeCreateUser($user, array $data)
    {
    }

}