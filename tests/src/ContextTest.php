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

namespace Brain\Context\Tests;

use Brain\Context\Context;
use Brain\Context\Provider;
use Brain\Context\Provider\ArrayMerge;
use Brain\Context\ProviderFactory;
use Brain\Monkey;
use Psr\Log\LoggerInterface;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context\Tests
 * @license http://opensource.org/licenses/MIT MIT
 */
class ContextTest extends TestCase
{
    /**
     * @test
     */
    public function testWorkflow(): void
    {
        $query = \Mockery::mock(\WP_Query::class);
        $query->allows('is_archive')->andReturn(true);
        $query->allows('is_date')->andReturn(true);
        $query->allows('is_singular')->andReturn(false);
        $query->allows('is_front_page')->andReturn(false);
        $query->is_archive = true;
        $query->is_date = true;
        $query->is_singular = false;
        $query->is_home = false;

        $wp = \Mockery::mock(\WP::class);
        $wp->query_vars = ['m' => 198209];

        $GLOBALS['wp_query'] = $query;
        $GLOBALS['wp'] = $wp;

        $front = new class implements ProviderFactory
        {
            public function create(\WP_Query $query, LoggerInterface $logger): ?Provider
            {
                if (!$query->is_front_page()) {
                    return null;
                }

                return new Provider\ByArray(['Welcome' => 'Welcome!']);
            }
        };

        $archive = new class implements ProviderFactory
        {
            public function create(\WP_Query $query, LoggerInterface $logger): ?Provider
            {
                if (!$query->is_archive()) {
                    return null;
                }

                $post1 = \Mockery::mock(\WP_Post::class);
                $post1->allows('to_array')->andReturn(['ID' => 1]);

                $post2 = \Mockery::mock(\WP_Post::class);
                $post2->allows('to_array')->andReturn(['ID' => 2]);

                $post3 = \Mockery::mock(\WP_Post::class);
                $post3->allows('to_array')->andReturn(['ID' => 3]);

                $postCb = static function () use ($post1): array {
                    return ['p1' => $post1];
                };

                Monkey\Functions\expect('get_posts')
                    ->once()
                    ->with(['posts_per_page' => 5])
                    ->andReturn([$post1, $post2, $post3]);

                return ArrayMerge::new()
                    ->addProvider(new Provider\ByCallback($postCb))
                    ->addProvider(new Provider\Posts(['posts_per_page' => 5]));
            }
        };

        $date = new class implements ProviderFactory
        {
            public function create(\WP_Query $query, LoggerInterface $logger): ?Provider
            {
                if (!$query->is_date()) {
                    return null;
                }

                $term = \Mockery::mock(\WP_Term::class);
                $term->allows('to_array')->andReturn(['term_id' => 123, 'taxonomy' => 'category']);

                Monkey\Functions\expect('get_terms')
                    ->once()
                    ->with(['taxonomy' => 'category'])
                    ->andReturn([$term]);

                return ArrayMerge::new()
                    ->addProvider(new Provider\ByArray(['tax' => 'category']))
                    ->addProvider(new Provider\Terms(['taxonomy' => 'category']));
            }
        };

        $singular = new class implements ProviderFactory
        {
            public function create(\WP_Query $query, LoggerInterface $logger): ?Provider
            {
                if (!$query->is_singular()) {
                    return null;
                }

                $post = \Mockery::mock(\WP_Post::class);
                $post->allows('to_array')->andReturn(['ID' => 123]);

                return ArrayMerge::new()
                    ->addProvider(new Provider\ByArray(['query' => ['ID' => 123]]))
                    ->addProvider(new Provider\ByArray(['single_post' => $post]));
            }
        };

        $all = [$front, $archive, $date, $singular];

        $context = Context::new();

        Monkey\Actions\expectDone(Context::ACTION_INIT_PROVIDERS)
            ->once()
            ->with($context)
            ->whenHappen(static function (Context $context) use ($all): void {
                foreach ($all as $providerFactory) {
                    $context->withProviderFactory($providerFactory);
                }
            });

        $expected = [
            'test' => 'Test!',
            'p1' => (object)['ID' => 1],
            'posts' => [(object)['ID' => 1], (object)['ID' => 2], (object)['ID' => 3]],
            'tax' => 'category',
            'category_terms' => [(object)['term_id' => 123, 'taxonomy' => 'category']],
            'wp_query' => (object)[
                'is_archive' => true,
                'is_date' => true,
                'is_singular' => false,
                'is_home' => false,
                'args' => [],
            ],
            'wp' => (object)['query_vars' => ['m' => 198209]],
        ];

        $data = $context
            ->withProvider(new Provider\ByArray(['test' => 'Test!']))
            ->convertEntitiesToPlainObjects()
            ->forwardGlobals()
            ->provide();

        static::assertEquals($expected, $data);
    }
}
