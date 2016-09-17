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

use Brain\Context\Provider\CommentsContextProvider;
use Brain\Monkey\Functions;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context
 * @license http://opensource.org/licenses/MIT MIT
 */
class CommentsContextProviderTest extends TestCase
{

    public function testProvide()
    {
        $provider = new CommentsContextProvider('test', ['foo' => 'bar']);

        Functions::expect('get_comments')->once()->with(['foo' => 'bar'])->andReturn([]);

        assertSame(['test' => []], $provider->provide());
    }
}
