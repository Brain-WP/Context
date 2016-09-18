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

use Brain\Context\QueryPredicate\Predicates;
use Brain\Context\Tests\TestCase;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context
 * @license http://opensource.org/licenses/MIT MIT
 */
class PredicatesTest extends TestCase
{
    public function testInvokeNoPredicates()
    {
        $predicates = new Predicates([]);
        $query = \Mockery::mock('WP_Query');

        assertFalse($predicates($query));
    }

    public function testInvokeAnd()
    {
        $query = \Mockery::mock('WP_Query');
        $query->shouldReceive('is_single')->andReturn(false);
        $query->shouldReceive('is_singular')->andReturn(true);
        $query->shouldReceive('is_page')->andReturn(true);

        $mode = Predicates::MODE_AND;

        $predicates_all = new Predicates(['is_single', 'is_singular', 'is_page'], $mode);
        $predicates_page = new Predicates(['is_singular', 'is_page']);

        assertFalse($predicates_all($query));
        assertTrue($predicates_page($query));
    }

    public function testInvokeOr()
    {
        $query = \Mockery::mock('WP_Query');
        $query->shouldReceive('is_single')->andReturn(false);
        $query->shouldReceive('is_singular')->andReturn(true);
        $query->shouldReceive('is_page')->andReturn(true);
        $query->shouldReceive('is_archive')->andReturn(false);
        $query->shouldReceive('is_search')->andReturn(false);

        $mode = Predicates::MODE_OR;

        $predicates_a = new Predicates(['is_archive', 'is_single'], $mode);
        $predicates_b = new Predicates(['is_search', 'is_singular'], $mode);
        $predicates_c = new Predicates(['is_archive', 'is_page'], $mode);
        $predicates_d = new Predicates(['is_search', 'is_archive'], $mode);
        $predicates_e = new Predicates(['is_page', 'is_singular'], $mode);

        assertFalse($predicates_a($query));
        assertTrue($predicates_b($query));
        assertTrue($predicates_c($query));
        assertFalse($predicates_d($query));
        assertTrue($predicates_e($query));
    }
}
