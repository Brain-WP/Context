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

use Brain\Context\Provider\Comments;
use Brain\Context\Tests\TestCase;
use Brain\Monkey\Functions;
use Psr\Log\NullLogger;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context\Tests
 * @license http://opensource.org/licenses/MIT MIT
 */
class CommentsTest extends TestCase
{
    /**
     * @test
     */
    public function testProvideCustomKey(): void
    {
        $provider = new Comments(['foo' => 'bar'], 'test');

        $c1 = \Mockery::mock(\WP_Comment::class);
        $c2 = clone $c1;

        $query = \Mockery::mock(\WP_Query::class);
        $logger = new NullLogger();

        Functions\expect('get_comments')->once()->with(['foo' => 'bar'])->andReturn([$c1, $c2]);

        static::assertSame(['test' => [$c1, $c2]], $provider->provide($query, $logger));
    }

    /**
     * @test
     */
    public function testProvideDefaultKey(): void
    {
        $provider = new Comments(['foo' => 'bar']);

        $c1 = \Mockery::mock(\WP_Comment::class);
        $c2 = clone $c1;

        $query = \Mockery::mock(\WP_Query::class);
        $logger = new NullLogger();

        Functions\expect('get_comments')->once()->with(['foo' => 'bar'])->andReturn([$c1, $c2]);

        static::assertSame(['comments' => [$c1, $c2]], $provider->provide($query, $logger));
    }
}
