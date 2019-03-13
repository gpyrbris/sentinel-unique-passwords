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
use PHPUnit_Framework_TestCase;

abstract class UniquePasswordsTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Adds a mock connection to the object.
     *
     * @param  mixed  $model
     * @return mixed
     */
    protected function addMockConnection($model)
    {
        $model->setConnectionResolver($resolver = m::mock('Illuminate\Database\ConnectionResolverInterface'));

        $resolver->shouldReceive('connection')
            ->andReturn(m::mock('Illuminate\Database\Connection'));

        $model->getConnection()
            ->shouldReceive('getQueryGrammar')
            ->andReturn(m::mock('Illuminate\Database\Query\Grammars\Grammar'));

        $model->getConnection()
            ->shouldReceive('getPostProcessor')
            ->andReturn(m::mock('Illuminate\Database\Query\Processors\Processor'));

        return $model;
    }
}
