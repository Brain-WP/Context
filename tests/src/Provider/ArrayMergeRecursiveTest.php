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
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context\Tests
 * @license http://opensource.org/licenses/MIT MIT
 */
class ArrayMergeRecursiveTest extends TestCase
{
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

        $data = Provider\ArrayMergeRecursive::new()
            ->addProvider($contextSingle1)
            ->addProvider($contextHomePage)
            ->addProvider($contextSingle2)
            ->provide($query, new NullLogger());

        static::assertSame(['one' => 'one', 'foo' => ['bar', 'baz']], $data);
    }
}
