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

use Brain\Context\Provider\Users;
use Brain\Context\Tests\TestCase;
use Brain\Monkey\Functions;
use Psr\Log\NullLogger;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context\Tests
 * @license http://opensource.org/licenses/MIT MIT
 */
class UsersTest extends TestCase
{
    /**
     * @test
     */
    public function testProvideCustomKey(): void
    {
        $provider = new Users(['foo' => 'bar'], 'test');

        $user1 = \Mockery::mock(\WP_Post::class);
        $user2 = clone $user1;

        $query = \Mockery::mock(\WP_Query::class);
        $logger = new NullLogger();

        Functions\expect('get_users')->once()->with(['foo' => 'bar'])->andReturn([$user1, $user2]);

        static::assertSame(['test' => [$user1, $user2]], $provider->provide($query, $logger));
    }

    /**
     * @test
     */
    public function testProvideDefaultKey(): void
    {
        $provider = new Users(['foo' => 'bar']);

        $user1 = \Mockery::mock(\WP_Post::class);
        $user2 = clone $user1;

        $query = \Mockery::mock(\WP_Query::class);
        $logger = new NullLogger();

        Functions\expect('get_users')->once()->with(['foo' => 'bar'])->andReturn([$user1, $user2]);

        static::assertSame(['users' => [$user1, $user2]], $provider->provide($query, $logger));
    }
}
