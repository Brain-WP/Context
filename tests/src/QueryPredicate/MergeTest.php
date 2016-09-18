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

use Brain\Context\QueryPredicate\Merge;
use Brain\Context\QueryPredicate\Predicates;
use Brain\Context\Tests\TestCase;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context
 * @license http://opensource.org/licenses/MIT MIT
 */
class MergeTest extends TestCase
{
    public function testInvokeNoPredicates()
    {
        $merge = new Merge([]);
        $query = \Mockery::mock('WP_Query');

        assertFalse($merge($query));
    }

    public function testInvokeAnd()
    {
        $by2 = function (\WP_Query $query) {

            return $query->queried_object_id % 2 === 0;
        };

        $by4 = function (\WP_Query $query) {

            return $query->queried_object_id % 4 === 0;
        };

        $by3 = function (\WP_Query $query) {

            return $query->queried_object_id % 3 === 0;
        };

        $query_12 = \Mockery::mock('WP_Query');
        $query_8 = clone $query_12;
        $query_6 = clone $query_12;

        $query_12->queried_object_id = 12;
        $query_8->queried_object_id = 8;
        $query_6->queried_object_id = 6;

        $merge = new Merge([$by2, $by4, $by3]);

        assertTrue($merge($query_12));
        assertFalse($merge($query_8));
        assertFalse($merge($query_6));
    }

    public function testInvokeOr()
    {
        $by2 = function (\WP_Query $query) {

            return $query->queried_object_id % 2 === 0;
        };

        $by3 = function (\WP_Query $query) {

            return $query->queried_object_id % 3 === 0;
        };

        $query_12 = \Mockery::mock('WP_Query');
        $query_8 = clone $query_12;
        $query_9 = clone $query_12;
        $query_7 = clone $query_12;

        $query_12->queried_object_id = 12;
        $query_8->queried_object_id = 8;
        $query_9->queried_object_id = 9;
        $query_7->queried_object_id = 7;

        $merge = new Merge([$by2, $by3], Predicates::MODE_OR);

        assertTrue($merge($query_12));
        assertTrue($merge($query_8));
        assertTrue($merge($query_9));
        assertFalse($merge($query_7));
    }
}
