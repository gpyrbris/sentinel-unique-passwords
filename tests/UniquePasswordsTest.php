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

namespace Cartalyst\Sentinel\Addons\UniquePasswords\tests;

use Mockery as m;
use Cartalyst\Sentinel\Addons\UniquePasswords\UniquePasswords;
use Cartalyst\Sentinel\Addons\UniquePasswords\Exceptions\NotUniquePasswordException;

class UniquePasswordsTest extends UniquePasswordsTestCase
{
    /**
     * Close mockery.
     *
     * @return void
     */
    public function tearDown()
    {
        m::close();
    }

    /** @test */
    public function it_stores_the_users_password_on_create()
    {
        $user = new \Cartalyst\Sentinel\Users\EloquentUser;

        $model = $this->addMockConnection($user);

        $model->getConnection()
            ->getQueryGrammar()
            ->shouldReceive('getDateFormat');

        $model->getConnection()
            ->getQueryGrammar()
            ->shouldReceive('compileInsertGetId');

        $model->getConnection()
            ->getPostProcessor()
            ->shouldReceive('processInsertGetId');

        $credentials = [
            'email' => 'foo@bar.com',
            'password' => 'secret',
        ];

        $uniquePasswords = new UniquePasswords($users = m::mock('Cartalyst\Sentinel\Users\UserRepositoryInterface'));

        $uniquePasswords->created($user, $credentials);
    }

    /** @test */
    public function it_stores_the_users_password_on_update()
    {
        $user = new \Cartalyst\Sentinel\Users\EloquentUser;

        $user->id = 1;

        $model = $this->addMockConnection($user);

        $model->getConnection()
            ->getQueryGrammar()
            ->shouldReceive('getDateFormat');

        $model->getConnection()
            ->getQueryGrammar()
            ->shouldReceive('compileSelect');

        $model->getConnection()
            ->shouldReceive('select');

        $model->getConnection()
            ->getPostProcessor()
            ->shouldReceive('processSelect')

            ->andReturn(['secret']);
        $model->getConnection()
            ->getQueryGrammar()
            ->shouldReceive('compileInsertGetId');

        $model->getConnection()
            ->getPostProcessor()
            ->shouldReceive('processInsertGetId');

        $credentials = [
            'email' => 'foo@bar.com',
            'password' => 'secret',
        ];

        $uniquePasswords = new UniquePasswords($users = m::mock('Cartalyst\Sentinel\Users\UserRepositoryInterface'));

        $users->shouldReceive('getHasher')
            ->andReturn($hasher = m::mock('Cartalyst\Sentinel\Hashing\HasherInterface'));

        $hasher->shouldReceive('check');

        $uniquePasswords->filled($user, $credentials);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_on_update_if_the_password_has_been_used()
    {
        $user = new \Cartalyst\Sentinel\Users\EloquentUser;

        $user->id = 1;

        $model = $this->addMockConnection($user);

        $model->getConnection()
            ->getQueryGrammar()
            ->shouldReceive('getDateFormat');

        $model->getConnection()
            ->getQueryGrammar()
            ->shouldReceive('compileSelect');

        $model->getConnection()
            ->shouldReceive('select');

        $model->getConnection()
            ->getPostProcessor()
            ->shouldReceive('processSelect')
            ->andReturn(['secret']);

        $model->getConnection()
            ->getQueryGrammar()
            ->shouldReceive('compileInsertGetId');

        $model->getConnection()
            ->getPostProcessor()
            ->shouldReceive('processInsertGetId');

        $credentials = [
            'email' => 'foo@bar.com',
            'password' => 'secret',
        ];

        $uniquePasswords = new UniquePasswords($users = m::mock('Cartalyst\Sentinel\Users\UserRepositoryInterface'));

        $users->shouldReceive('getHasher')
            ->andReturn($hasher = m::mock('Cartalyst\Sentinel\Hashing\HasherInterface'));

        $hasher->shouldReceive('check')
            ->andReturn(true);

        try {
            $uniquePasswords->filled($user, $credentials);
        } catch (NotUniquePasswordException $e) {
            $this->assertSame($e->getUser(), $user);
        }
    }

    /** @test */
    public function it_flushed_passwords_on_delete()
    {
        $user = new \Cartalyst\Sentinel\Users\EloquentUser;

        $model = $this->addMockConnection($user);

        $model->getConnection()
            ->getQueryGrammar()
            ->shouldReceive('getDateFormat');

        $model->getConnection()
            ->getQueryGrammar()
            ->shouldReceive('compileSelect');

        $model->getConnection()
            ->shouldReceive('delete');

        $model->getConnection()
            ->getPostProcessor()
            ->shouldReceive('processSelect')
            ->andReturn(['secret']);

        $model->getConnection()
            ->getQueryGrammar()
            ->shouldReceive('compileDelete');

        $model->getConnection()
            ->getPostProcessor()
            ->shouldReceive('processInsertGetId');

        $credentials = [
            'email' => 'foo@bar.com',
            'password' => 'secret',
        ];

        $uniquePasswords = new UniquePasswords($users = m::mock('Cartalyst\Sentinel\Users\UserRepositoryInterface'));

        $users->shouldReceive('getHasher')
            ->andReturn($hasher = m::mock('Cartalyst\Sentinel\Hashing\HasherInterface'));

        $hasher->shouldReceive('check');

        $uniquePasswords->deleted($user);
    }
}
