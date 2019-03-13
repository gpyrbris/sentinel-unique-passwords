<?php

/**
 * Part of the Sentinel Unique Passwords addon.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Sentinel Unique Passwords
 * @version    2.0.2
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2017, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Cartalyst\Sentinel\Addons\UniquePasswords;

use Cartalyst\Support\Traits\RepositoryTrait;
use Cartalyst\Sentinel\Users\UserRepositoryInterface;
use Cartalyst\Sentinel\Addons\UniquePasswords\Exceptions\NotUniquePasswordException;

class UniquePasswords
{
    use RepositoryTrait;

    /**
     * The Sentinel User repository.
     *
     * @var \Cartalyst\Sentinel\Users\UserRepositoryInterface
     */
    protected $users;

    /**
     * The model name.
     *
     * @var string
     */
    protected $model = 'Cartalyst\Sentinel\Addons\UniquePasswords\Models\Password';

    /**
     * Constructor.
     *
     * @param  \Cartalyst\Sentinel\Users\UserRepositoryInterface  $users
     * @return void
     */
    public function __construct(UserRepositoryInterface $users)
    {
        $this->users = $users;
    }

    /**
     * User created event.
     *
     * @return void
     */
    public function created()
    {
        list($user, $credentials) = $this->extractArgs(func_get_args());

        $this->storePassword($user->id, $user->password);
    }

    /**
     * User deleted event.
     *
     * @param  \Cartalyst\Sentinel\Users\UserInterface  $user
     * @return void
     */
    public function deleted($user)
    {
        $this->flushPasswords($user->id);
    }

    /**
     * User filled event.
     *
     * @return void
     * @throws \Cartalyst\Sentinel\Addons\UniquePasswords\Exceptions\NotUniquePasswordException
     */
    public function filled()
    {
        list($user, $credentials) = $this->extractArgs(func_get_args());

        if (($password = array_get($credentials, 'password')) && $user->id) {
            $userId = $user->id;

            $userPassword = $user->password;

            if ($this->findPreviousPassword($password, $userId, $userPassword)) {
                $exception = new NotUniquePasswordException('This password was already used before!');

                $exception->setUser($user);

                throw $exception;
            }
        }
    }

    /**
     * Checks if the password was already used.
     *
     * @param  string  $password
     * @param  string  $userId
     * @param  string  $userPassword
     * @return bool
     */
    protected function findPreviousPassword($password, $userId, $userPassword)
    {
        $passwords = $this->createModel()->where('user_id', $userId)->get();

        $usedBefore = false;

        foreach ($passwords as $_password) {
            if ($this->users->getHasher()->check($password, $_password->password)) {
                $usedBefore = true;

                break;
            }
        }

        if (! $usedBefore) {
            $this->storePassword($userId, $userPassword);
        }

        return $usedBefore;
    }

    /**
     * Stores a user password.
     *
     * @param  string  $userId
     * @param  string  $userPass
     * @return void
     */
    protected function storePassword($userId, $userPassword)
    {
        $password = $this->createModel();

        $password->user_id = $userId;

        $password->password = $userPassword;

        $password->save();
    }

    /**
     * Clears a user's passwords.
     *
     * @param  string  $userId
     * @return int
     */
    protected function flushPasswords($userId)
    {
        return $this->createModel()->where('user_id', $userId)->delete();
    }

    /**
     * Extracts the arguments.
     *
     * @param  array  $args
     * @return array
     */
    protected function extractArgs($args)
    {
        $user = array_shift($args);

        $credentials = array_shift($args);

        return [ $user, $credentials ];
    }
}
