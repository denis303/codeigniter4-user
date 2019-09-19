<?php

namespace denis303\user;

use Exception;
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

    public static function findByEmail($email)
    {
        $class = get_called_class();

        $model = new $class;

        return $model->where([$model::FIELD_PREFIX . 'email' => $email])->first();
    }

    public static function createUser(array $data, &$error = null)
    {
        $class = get_called_class();

        $model = new $class;

        $class = $model->returnType;

        $user = new $class;

        if (array_key_exists($model::FIELD_PREFIX . 'password', $data))
        {
            $this->setPassword($user, $data[$model::FIELD_PREFIX . 'password']);

            unset($data[$model::FIELD_PREFIX . 'password']);
        }

        foreach($data as $key => $value)
        {
            $user->$key = $value;
        }

        $model->beforeCreateUser($user, $data);

        if (!$model->save($user))
        {
            $errors = $model->errors();

            $error = array_shift($errors);

            return false;
        }

        return $user;
    }

    public function beforeCreateUser($user, array $data)
    {
    }    

    public function setUserPassword($user, string $password)
    {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
    
        static::setField($user, 'password_hash', $password_hash);
    }

    public function validateUserPassword($user, string $password) : bool
    {
        $password_hash = static::getField($user, 'password_hash');

        return password_verify($password, $password_hash);
    }

    public function getUserField($user, string $field, bool $applyPrefix = true)
    {
        if ($applyPrefix)
        {
            $field = UserModel::FIELD_PREFIX . $field;
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

    public function setUserField($user, string $field, $value, bool $applyPrefix = true)
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

    public function getUserEmail($user)
    {
        return $this->getUserField($user, 'email', true);
    }

    public function getUserName($user)
    {
        return $this->getUserField($user, 'name', true);
    }    

}