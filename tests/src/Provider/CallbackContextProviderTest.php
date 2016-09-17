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

        assertSame(['foo', 'bar'], $provider->provide());
    }
}
