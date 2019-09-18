<?php

namespace denis303\user;

use Exception;
use Config\Services;

abstract class BaseUserModel extends \App\Components\BaseModel
{

    const FIELD_PREFIX = 'user_';

    protected $table = 'user';

    protected $primaryKey = self::FIELD_PREFIX . 'id';

    protected $defaultStatus = null;

    protected $allowedFields = [
        self::FIELD_PREFIX . 'name',
        self::FIELD_PREFIX . 'password_hash',
        self::FIELD_PREFIX . 'email',
        self::FIELD_PREFIX . 'created_at',
        self::FIELD_PREFIX . 'updated_at'
    ];

    protected $returnType = Entity::class;

    public function createUser(array $data, & $error = null)
    {
        $class = $this->returnType;

        $user = new $class;

        if (array_key_exists(static::FIELD_PREFIX . 'password', $data))
        {
            $this->setPassword($user, $data[static::FIELD_PREFIX . 'password']);

            unset($data[static::FIELD_PREFIX . 'password']);
        }

        foreach($data as $key => $value)
        {
            $this->setUserField($user, $key, $value);
        }

        $this->beforeCreateUser($user, $data);

        if (!$this->save($user))
        {
            $error = $this->firstError();

            return false;
        }

        return $user;
    }

    public function beforeCreateUser($user, array $data)
    {
    }

    public function setPassword($user, string $password)
    {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
    
        $this->setUserField($user, 'password_hash', $password_hash);
    }

    public function setStatus($user, $status)
    {
        $user->status = $status;
    }

    public function validatePassword($user, string $password) : bool
    {
        $password_hash = $this->getUserField($user, 'password_hash');

        return password_verify($password, $password_hash);
    }

    public function getUserField($user, string $field)
    {
        $field = static::FIELD_PREFIX . $field;

        if (is_array($user))
        {
            return $user[$field];
        }
        else
        {
            return $user->$field;
        }
    }

    public function setUserField($user, string $field, $value)
    {
        $field = static::FIELD_PREFIX . $field;

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
        return $this->getUserField($user, 'email');
    }

    public function getUserName($user)
    {
        return $this->getUserField($user, 'name');
    }

}