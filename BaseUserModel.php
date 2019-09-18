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
            $user->$key = $value;
        }

        $this->beforeCreateUser($user, $data);

        if (!$this->save($user))
        {
            $error = $this->firstError();

            return false;
        }

        return $user;
    }

    public function beforeCreateUser(Entity $user, array $data)
    {
    }

    public function setPassword(Entity $user, string $password)
    {
        $user->{static::FIELD_PREFIX . 'password_hash'} = password_hash($password, PASSWORD_BCRYPT);
    }

    public function setStatus(Entity $user, $status)
    {
        $user->status = $status;
    }

    public function validatePassword(Entity $user, string $password) : bool
    {
        return password_verify($password, $user->{static::FIELD_PREFIX . 'password_hash'});
    }

    public function getUserEmail(Entity $user)
    {
        return $user->{static::FIELD_PREFIX . 'email'};
    }

    public function getUserName(Entity $user)
    {
        return $user->{static::FIELD_PREFIX . 'name'};
    }    

}