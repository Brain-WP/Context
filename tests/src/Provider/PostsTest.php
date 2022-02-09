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

use Brain\Context\Provider\Posts;
use Brain\Context\Tests\TestCase;
use Brain\Monkey\Functions;
use Psr\Log\NullLogger;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context\Tests
 * @license http://opensource.org/licenses/MIT MIT
 */
class PostsTest extends TestCase
{
    /**
     * @test
     */
    public function testProvideCustomKey(): void
    {
        $provider = new Posts(['foo' => 'bar'], 'test');

        $post1 = \Mockery::mock(\WP_Post::class);
        $post2 = clone $post1;

        $query = \Mockery::mock(\WP_Query::class);
        $logger = new NullLogger();

        Functions\expect('get_posts')->once()->with(['foo' => 'bar'])->andReturn([$post1, $post2]);

        static::assertSame(['test' => [$post1, $post2]], $provider->provide($query, $logger));
    }

    /**
     * @test
     */
    public function testProvideDefaultKey(): void
    {
        $provider = new Posts(['foo' => 'bar']);

        $post1 = \Mockery::mock(\WP_Post::class);
        $post2 = clone $post1;

        $query = \Mockery::mock(\WP_Query::class);
        $logger = new NullLogger();

        Functions\expect('get_posts')->once()->with(['foo' => 'bar'])->andReturn([$post1, $post2]);

        static::assertSame(['posts' => [$post1, $post2]], $provider->provide($query, $logger));
    }
}
