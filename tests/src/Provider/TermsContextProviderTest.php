<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the Context package.
 *
 * (c) Giuseppe Mazzapica
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brain\Context\Tests\Provider;

use Brain\Context\Provider\TermsContextProvider;
use Brain\Monkey\Functions;
use Brain\Context\Tests\TestCase;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context
 * @license http://opensource.org/licenses/MIT MIT
 */
class TermsContextProviderTest extends TestCase
{

    public function testProvide()
    {
        $provider = new TermsContextProvider('test', ['foo' => 'bar']);

        Functions::expect('get_terms')->once()->with(['foo' => 'bar'])->andReturn(['x', 'y']);

        assertSame(['test' => ['x', 'y']], $provider->provide());
    }
}
