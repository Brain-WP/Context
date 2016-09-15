<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the Context package.
 *
 * (c) Giuseppe Mazzapica
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brain\Context\Tests;

use Brain\Context\ArrayMergeRecursiveContextCollector;
use Brain\Context\ContextProviderInterface;
use Brain\Context\ArrayMergeContextCollector;
use Brain\Monkey\WP\Actions;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context
 * @license http://opensource.org/licenses/MIT MIT
 */
class ArrayMergeRecursiveContextCollectorTest extends TestCase
{

    public function testProvide()
    {
        $collector = new ArrayMergeRecursiveContextCollector();

        $query = \Mockery::mock('WP_Query');

        $context_a = \Mockery::mock(ContextProviderInterface::class);
        $context_b = clone $context_a;
        $context_c = clone $context_b;

        $context_a->shouldReceive('accept')->once()->with($query)->andReturn(true);
        $context_b->shouldReceive('accept')->once()->with($query)->andReturn(false);
        $context_c->shouldReceive('accept')->once()->with($query)->andReturn(true);

        $context_a->shouldReceive('provide')->once()->andReturn([
            'message' => 'Hello from A!',
            'letters' => ['a']
        ]);

        $context_b->shouldReceive('provide')->once()->andReturn([
            'message' => 'Goodbye from B!',
            'meh'     => 'meh'
        ]);

        $context_c->shouldReceive('provide')->once()->andReturn([
            'message' => 'Hello from C!',
            'letters' => ['b', 'c', 'd'],
            'color'   => 'yellow'
        ]);

        Actions::expectFired('brain.context.added')
            ->times(3)
            ->with(\Mockery::type(ContextProviderInterface::class), \Mockery::type('SplQueue'));

        $collector
            ->addProvider($context_a)
            ->addProvider($context_b)
            ->addProvider($context_c);

        $collector->accept($query);

        $expected = [
            'message' => ['Hello from A!', 'Hello from C!'],
            'letters' => ['a', 'b', 'c', 'd'],
            'color'   => 'yellow'
        ];

        assertSame($expected, $collector->provide());
    }

}