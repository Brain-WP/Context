<?php

/*
 * This file is part of the Context package.
 *
 * (c) Giuseppe Mazzapica
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Brain\Context\Tests\Provider;

use Brain\Context\Provider;
use Brain\Context\Tests\TestCase;
use Psr\Log\NullLogger;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context\Tests
 * @license http://opensource.org/licenses/MIT MIT
 */
class ByArrayTest extends TestCase
{
    /**
     * @test
     */
    public function testMerge(): void
    {
        $data = ['one' => 'one', 'two' => new \stdClass()];
        $provider = new Provider\ByArray($data);

        $query = \Mockery::mock(\WP_Query::class);
        $logger = new NullLogger();

        static::assertSame($data, $provider->provide($query, $logger));
    }
}
