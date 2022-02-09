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
class WpEntitiesFlattenerTest extends TestCase
{
    /**
     * @test
     */
    public function testProvide(): void
    {
        $postProvider = new class() implements Provider
        {
            public function provide(\WP_Query $query, LoggerInterface $logger): ?array
            {
                $post = \Mockery::mock(\WP_Post::class);
                $post->expects('to_array')->andReturn([
                    'ID' => 1,
                    'post_name' => 'test',
                    'post_title' => 'Test',
                    'post_content' => 'This is a test',
                ]);

                return compact('post');
            }
        };

        $termProvider = new class() implements Provider
        {
            public function provide(\WP_Query $query, LoggerInterface $logger): ?array
            {
                $term = \Mockery::mock(\WP_Term::class);
                $term->expects('to_array')->andReturn([
                    'term_id' => 2,
                    'slug' => 'test',
                    'name' => 'Test',
                    'taxonomy' => 'category',
                ]);

                return compact('term');
            }
        };

        $merged = Provider\ArrayMergeRecursive::new()
            ->addProvider($postProvider)
            ->addProvider($termProvider);

        $expected = [
            'post' => (object)[
                'ID' => 1,
                'post_name' => 'test',
                'post_title' => 'Test',
                'post_content' => 'This is a test',
            ],
            'term' => (object)[
                'term_id' => 2,
                'slug' => 'test',
                'name' => 'Test',
                'taxonomy' => 'category',
            ]
        ];

        $provider = new Provider\WpEntitiesFlattener($merged);
        $query = \Mockery::mock(\WP_Query::class);
        $logger = new NullLogger();

        static::assertEquals($expected, $provider->provide($query, $logger));
    }
}
