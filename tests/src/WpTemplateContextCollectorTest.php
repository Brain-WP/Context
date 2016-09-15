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

use Andrew\Proxy;
use Brain\Context\ContextProviderInterface;
use Brain\Context\WpTemplateContextCollector;
use Brain\Monkey\WP\Actions;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context
 * @license http://opensource.org/licenses/MIT MIT
 */
class WpTemplateContextCollectorTest extends TestCase
{

    public function testAddProvider()
    {
        $collector = new WpTemplateContextCollector();

        $context_a = \Mockery::mock(ContextProviderInterface::class);
        $context_b = clone $context_a;
        $context_c = clone $context_b;

        $context_a->id = 'a';
        $context_b->id = 'b';
        $context_c->id = 'c';

        Actions::expectFired('brain.context.added')
            ->times(3)
            ->with(\Mockery::type(ContextProviderInterface::class), \Mockery::type('SplQueue'))
            ->whenHappen(function ($context, \SplQueue $providers) {
                if ($context->id === 'c') {
                    $providers->pop();
                }
            });

        $collector
            ->addProvider($context_a)
            ->addProvider($context_b)
            ->addProvider($context_c);

        $proxy = new Proxy($collector);
        /** @var \SplQueue $providers */
        $providers = $proxy->providers;

        assertCount(2, $providers);
        assertSame('a', $providers->dequeue()->id);
        assertSame('b', $providers->dequeue()->id);
    }

    public function testAccept()
    {
        $collector = new WpTemplateContextCollector();

        $query = \Mockery::mock('WP_Query');
        $accepted = $collector->accept($query);

        $proxy = new Proxy($collector);
        /** @var \WP_Query $saved_query */
        $saved_query = $proxy->query;

        assertTrue($accepted);
        assertSame($query, $saved_query);
    }

    public function testProvide()
    {
        $collector = new WpTemplateContextCollector();

        $query = \Mockery::mock('WP_Query');

        $context_a = \Mockery::mock(ContextProviderInterface::class);
        $context_b = clone $context_a;
        $context_c = clone $context_b;

        $context_a->shouldReceive('accept')->once()->with($query)->andReturn(true);
        $context_b->shouldReceive('accept')->once()->with($query)->andReturn(false);
        $context_c->shouldReceive('accept')->once()->with($query)->andReturn(true);

        $context_a->shouldReceive('provide')->once()->andReturn([
            'message' => 'Hello!',
            'letters' => ['a']
        ]);

        $context_b->shouldReceive('provide')->once()->andReturn([
            'message' => 'Goodbye!',
            'meh'     => 'meh'
        ]);

        $context_c->shouldReceive('provide')->once()->andReturn([
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
            'message' => 'Hello!',
            'letters' => ['b', 'c', 'd'],
            'color'   => 'yellow'
        ];

        assertSame($expected, $collector->provide());
    }

}