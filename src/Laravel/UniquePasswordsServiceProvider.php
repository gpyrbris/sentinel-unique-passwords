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

namespace Cartalyst\Sentinel\Addons\UniquePasswords\Laravel;

use Illuminate\Support\ServiceProvider;
use Cartalyst\Sentinel\Addons\UniquePasswords\UniquePasswords;

class UniquePasswordsServiceProvider extends ServiceProvider
{
    /**
     * {@inheritDoc}
     */
    public function boot()
    {
        $model = $this->app['sentinel.users']->getModel();

        // User created.
        $this->app['events']->listen('sentinel.user.created', function ($user, $credentials) {
            $this->app['sentinel.addons.unique-passwords']->created($user, $credentials);
        });

        // User updated.
        $this->app['events']->listen('sentinel.user.filled', function ($user, $credentials) {
            $this->app['sentinel.addons.unique-passwords']->filled($user, $credentials);
        });

        // User deleted.
        $this->app['events']->listen("eloquent.deleted: {$model}", function ($user) {
            $this->app['sentinel.addons.unique-passwords']->deleted($user);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $this->prepareResources();

        $this->app->singleton('sentinel.addons.unique-passwords', function ($app) {
            return new UniquePasswords($app['sentinel.users']);
        });
    }

    /**
     * Prepare the package resources.
     *
     * @return void
     */
    protected function prepareResources()
    {
        $migrations = realpath(__DIR__.'/../migrations');

        $this->publishes([
            $migrations => $this->app->databasePath().'/migrations',
        ], 'migrations');
    }
}
