<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the Context package.
 *
 * (c) Giuseppe Mazzapica
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brain\Context\Tests\QueryPredicate;

use Brain\Context\QueryPredicate\Predicate;
use Brain\Context\Tests\TestCase;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context
 * @license http://opensource.org/licenses/MIT MIT
 */
class PredicateTest extends TestCase
{
    public function testInvokeNoCondition()
    {
        $predicate = new Predicate('');
        $query = \Mockery::mock('WP_Query');

        assertFalse($predicate($query));
    }

    public function testInvokeNoParam()
    {
        $foo = new Predicate('foo');
        $bar = new Predicate('bar');

        $query = \Mockery::mock('WP_Query');
        $query->shouldReceive('foo')->once()->withNoArgs()->andReturn(false);
        $query->shouldReceive('bar')->once()->withNoArgs()->andReturn(true);

        assertFalse($foo($query));
        assertTrue($bar($query));
    }

    public function testInvokeParam()
    {
        $foo = new Predicate('foo', 'x');
        $bar = new Predicate('bar', 'y');

        $query = \Mockery::mock('WP_Query');
        $query->shouldReceive('foo')->once()->with('x')->andReturn(false);
        $query->shouldReceive('bar')->once()->with('y')->andReturn(true);

        assertFalse($foo($query));
        assertTrue($bar($query));
    }
}
