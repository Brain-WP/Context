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

use Brain\Context\Provider\Terms;
use Brain\Context\Tests\TestCase;
use Brain\Monkey\Functions;
use Psr\Log\NullLogger;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context\Tests
 * @license http://opensource.org/licenses/MIT MIT
 */
class TermsTest extends TestCase
{
    /**
     * @test
     */
    public function testProvideCustomKey(): void
    {
        $provider = new Terms(['foo' => 'bar'], 'test');

        $term1 = \Mockery::mock(\WP_Term::class);
        $term2 = clone $term1;

        $query = \Mockery::mock(\WP_Query::class);
        $logger = new NullLogger();

        Functions\expect('get_terms')->once()->with(['foo' => 'bar'])->andReturn([$term1, $term2]);

        static::assertSame(['test' => [$term1, $term2]], $provider->provide($query, $logger));
    }

    /**
     * @test
     */
    public function testProvideDefaultKey(): void
    {
        $provider = new Terms(['taxonomy' => 'category']);

        $term1 = \Mockery::mock(\WP_Post::class);
        $term2 = clone $term1;

        $query = \Mockery::mock(\WP_Query::class);
        $logger = new NullLogger();

        Functions\expect('get_terms')
            ->once()
            ->with(['taxonomy' => 'category'])
            ->andReturn([$term1, $term2]);

        static::assertSame(
            ['category_terms' => [$term1, $term2]],
            $provider->provide($query, $logger)
        );
    }

    /**
     * @test
     */
    public function testProvideDefaultKeyMultipleTaxonomies(): void
    {
        $provider = new Terms(['taxonomy' => ['post_tag', 'category']]);

        $term1 = \Mockery::mock(\WP_Post::class);
        $term2 = clone $term1;

        $query = \Mockery::mock(\WP_Query::class);
        $logger = new NullLogger();

        Functions\expect('get_terms')
            ->once()
            ->with(['taxonomy' => ['post_tag', 'category']])
            ->andReturn([$term1, $term2]);

        static::assertSame(
            ['category_post_tag_terms' => [$term1, $term2]],
            $provider->provide($query, $logger)
        );
    }
}
