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

require 'UniquePasswordsTestCase.php';

use Mockery as m;
use Cartalyst\Sentinel\Addons\UniquePasswords\Models\Password;

class PasswordTest extends UniquePasswordsTestCase
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

    /**
     * @test
     * @runInSeparateProcess
     */
    public function it_can_set_the_user_model()
    {
        $this->assertEquals(Password::getUsersModel(), 'Cartalyst\Sentinel\Users\EloquentUser');

        Password::setUsersModel('User');

        $this->assertEquals(Password::getUsersModel(), 'User');
    }

    /** @test */
    public function it_has_a_users_relationship()
    {
        $password = new Password;

        $this->addMockConnection($password);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsTo', $password->user());
    }
}
