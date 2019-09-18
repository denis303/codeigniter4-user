<?php

namespace denis303\user;

use Exception;
use Config\Services;
use App\Models\User;

abstract class BaseUserModel extends \App\Components\BaseModel
{

    const FIELD_PREFIX = 'user_';

    protected $table = 'user';

    protected $primaryKey = self::FIELD_PREFIX . 'id';

    protected $defaultStatus = null;

    protected $allowedFields = [
        self::FIELD_PREFIX . 'name',
        self::FIELD_PREFIX . 'password_hash',
        self::FIELD_PREFIX . 'password_reset_token',
        self::FIELD_PREFIX . 'verification_token',
        self::FIELD_PREFIX . 'email',
        self::FIELD_PREFIX . 'created_at',
        self::FIELD_PREFIX . 'updated_at'
    ];

    protected $returnType = User::class;

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

        if (!$user->user_verification_token)
        {
            $this->generateEmailVerificationToken($user);
        }

        if ($this->defaultStatus)
        {
            $this->setStatus($user, $this->defaultStatus);
        }

        if (!$this->save($user))
        {
            $error = $this->firstError();

            return false;
        }

        return $user;
    }

    public function setPassword(User $user, string $password)
    {
        $user->{static::FIELD_PREFIX . 'password_hash'} = password_hash($password, PASSWORD_BCRYPT);
    }

    public function setStatus(User $user, $status)
    {
        $user->status = $status;
    }

    public function generateEmailVerificationToken(User $user)
    {
        $user->{static::FIELD_PREFIX . 'verification_token'} = md5(time() . rand(0, PHP_INT_MAX));
    }

    public function validatePassword(User $user, string $password) : bool
    {
        return password_verify($password, $user->{static::FIELD_PREFIX . 'password_hash'});
    }

    public function getUserEmail(User $user)
    {
        return $user->{static::FIELD_PREFIX . 'email'};
    }

    public function getUserName(User $user)
    {
        return $user->{static::FIELD_PREFIX . 'name'};
    }    

}