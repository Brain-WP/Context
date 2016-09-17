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

use Brain\Context\Provider\SubqueryContextProvider;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context
 * @license http://opensource.org/licenses/MIT MIT
 */
class SubqueryContextProviderTest extends TestCase
{

    public function testProvide()
    {
        $provider = new SubqueryContextProvider('test', ['foo' => 'bar']);

        $context = $provider->provide();

        assertInternalType('array', $context);
        assertArrayHasKey('test', $context);
        assertInstanceOf('WP_Query', $context['test']);

        // see stubs/stubs.php
        assertSame([['foo' => 'bar']], $context['test']->args);
    }
}
