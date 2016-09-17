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

use Brain\Context\Provider\CallbackContextProvider;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context
 * @license http://opensource.org/licenses/MIT MIT
 */
class CallbackContextProviderTest extends TestCase
{

    public function testProvideDefault()
    {
        $provider = new CallbackContextProvider(function () {
            return ['foo', 'bar'];
        });

        $query = \Mockery::mock('WP_Query');

        assertTrue($provider->accept($query));
        assertSame(['foo', 'bar'], $provider->provide());
    }

    public function testAcceptCallback()
    {
        $query = \Mockery::mock('WP_Query');
        $query_2 = clone $query;

        $cb = function (\WP_Query $wp_query) use ($query) {
            return $wp_query === $query;
        };

        $provider = new CallbackContextProvider(function () {
            return ['foo', 'bar'];
        }, $cb);

        assertTrue($provider->accept($query));
        assertFalse($provider->accept($query_2));
        assertSame(['foo', 'bar'], $provider->provide());
    }
}
