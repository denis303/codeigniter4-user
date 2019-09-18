<?php

namespace denis303\user;

use Exception;
use Config\Services;

abstract class BaseUserModel extends \App\Components\BaseModel
{

    const STATUS_DELETED = 1;

    const STATUS_INACTIVE = 9;

    const STATUS_ACTIVE = 10;

    protected $table = 'user';

    protected $primaryKey = 'user_id';

    protected $allowedFields = [
        'user_name',
        'user_password_hash',
        'user_password_reset_token',
        'user_verification_token',
        'user_email',
        'user_created_at',
        'user_updated_at'
    ];

    protected $returnType = User::class;

    public function createUser(array $data, & $error = null)
    {
        $user = new User;

        if (array_key_exists('user_password', $data))
        {
            $this->setPassword($user, $data['user_password']);

            unset($data['user_password']);
        }

        foreach($data as $key => $value)
        {
            $user->$key = $value;
        }

        if (!$user->user_verification_token)
        {
            $this->generateEmailVerificationToken($user);
        }

        if (!$user->status)
        {
            $this->setStatusInactive($user);
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
        $user->user_password_hash = password_hash($password, PASSWORD_BCRYPT);
    }

    public function setStatus(User $user, $status)
    {
        $user->status = $status;
    }

    public function setStatusActive(User $user)
    {
        $this->setStatus(static::STATUS_ACTIVE);
    }

    public function setStatusInactive(User $user)
    {
        $this->setStatus(static::STATUS_INACTIVE);
    }    

    public function setStatusDeleted(User $user)
    {
        $this->setStatus(static::STATUS_INACTIVE);
    } 

    public function generateEmailVerificationToken(User $user)
    {
        $user->user_verification_token = md5(time() . rand(0, PHP_INT_MAX));
    }

    public function validatePassword(User $user, string $password) : bool
    {
        return password_verify($password, $user->user_password_hash);
    }

    public function getUserEmail(User $user)
    {
        return $user->user_email;
    }

    public function getUserName(User $user)
    {
        return $user->user_name;
    }    

}