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
use Brain\Context\Provider\ArrayMerge;
use Brain\Context\Tests\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context\Tests
 * @license http://opensource.org/licenses/MIT MIT
 */
class ArrayMergeTest extends TestCase
{
    /**
     * @test
     */
    public function testMerge(): void
    {
        $context1 = new class() implements Provider
        {
            public function provide(\WP_Query $query, LoggerInterface $logger): ?array
            {
                return ['one' => 'one'];
            }
        };

        $context2 = new class() implements Provider
        {
            public function provide(\WP_Query $query, LoggerInterface $logger): ?array
            {
                return ['two' => 'two'];
            }
        };

        $context3 = new class() implements Provider
        {
            public function provide(\WP_Query $query, LoggerInterface $logger): ?array
            {
                return ['three' => 'three'];
            }
        };

        $data = ArrayMerge::new()
            ->addProvider($context1)
            ->addProvider($context2)
            ->addProvider($context3)
            ->provide(\Mockery::mock(\WP_Query::class), new NullLogger());

        static::assertSame(['one' => 'one', 'two' => 'two', 'three' => 'three'], $data);
    }

    /**
     * @test
     */
    public function testConditionalMerge(): void
    {
        $contextSingle1 = new class() implements Provider
        {
            public function provide(\WP_Query $query, LoggerInterface $logger): ?array
            {
                return $query->is_single() ? ['one' => 'one', 'foo' => ['bar']] : null;
            }
        };

        $contextHomePage = new class() implements Provider
        {
            public function provide(\WP_Query $query, LoggerInterface $logger): ?array
            {
                return $query->is_front_page() ? ['two' => 'two'] : null;
            }
        };

        $contextSingle2 = new class() implements Provider
        {
            public function provide(\WP_Query $query, LoggerInterface $logger): ?array
            {
                return $query->is_single() ? ['foo' => ['baz']] : null;
            }
        };

        $query = \Mockery::mock(\WP_Query::class);
        $query->expects('is_single')->twice()->andReturn(true);
        $query->expects('is_front_page')->andReturn(false);

        $data = ArrayMerge::new()
            ->addProvider($contextSingle1)
            ->addProvider($contextHomePage)
            ->addProvider($contextSingle2)
            ->provide($query, new NullLogger());

        static::assertSame(['one' => 'one', 'foo' => ['baz']], $data);
    }
}
