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

use Brain\Context\Provider\UsersContextProvider;
use Brain\Monkey\Functions;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context
 * @license http://opensource.org/licenses/MIT MIT
 */
class UsersContextProviderTest extends TestCase
{

    public function testProvide()
    {
        $provider = new UsersContextProvider('test', ['foo' => 'bar']);

        Functions::expect('get_users')->once()->with(['foo' => 'bar'])->andReturn(['z', 'y']);

        assertSame(['test' => ['z', 'y']], $provider->provide());
    }
}
