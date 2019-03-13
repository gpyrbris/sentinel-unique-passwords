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

namespace Cartalyst\Sentinel\Addons\UniquePasswords\Models;

use Illuminate\Database\Eloquent\Model;

class Password extends Model
{
    /**
     * {@inheritDoc}
     */
    protected $table = 'passwords';

    /**
     * {@inheritDoc}
     */
    protected $fillable = [
        'password',
    ];

    /**
     * The users model name.
     *
     * @var string
     */
    protected static $usersModel = 'Cartalyst\Sentinel\Users\EloquentUser';

    /**
     * Returns the users model.
     *
     * @return string
     */
    public static function getUsersModel()
    {
        return static::$usersModel;
    }

    /**
     * Sets the users model.
     *
     * @param  string  $usersModel
     * @return void
     */
    public static function setUsersModel($usersModel)
    {
        static::$usersModel = $usersModel;
    }

    /**
     * The users relationship.
     *
     * @return \Cartalyst\Sentinel\Users\UserInterface
     */
    public function user()
    {
        return $this->belongsTo(static::$usersModel);
    }
}
