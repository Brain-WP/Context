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

use Brain\Context\QueryPredicate\Negate;
use Brain\Context\Tests\TestCase;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context
 * @license http://opensource.org/licenses/MIT MIT
 */
class NegateTest extends TestCase
{
    public function testInvoke()
    {
        $predicate = function (\WP_Query $query) {
            static $c = 0;
            $c++;

            return $c === $query->queried_object_id;
        };

        $negate = new Negate($predicate);

        $query = \Mockery::mock('WP_Query');
        $query->queried_object_id = 2;

        assertTrue($negate($query));
        assertFalse($negate($query));
        assertTrue($negate($query));
    }
}
