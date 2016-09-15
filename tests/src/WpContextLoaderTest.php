<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the Context package.
 *
 * (c) Giuseppe Mazzapica
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brain\Context\Tests;

use Brain\Context\ContextCollectorInterface;
use Brain\Context\ContextProviderInterface;
use Brain\Context\UpdatableContextProviderInterface;
use Brain\Context\WpContextLoader;
use Brain\Context\ArrayMergeContextCollector;
use Brain\Monkey\WP\Actions;
use Brain\Monkey\WP\Filters;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context
 * @license http://opensource.org/licenses/MIT MIT
 */
class WpContextLoaderTest extends TestCase
{

    public function testLoadNothingIfCollectorNotAcceptQuery()
    {
        $query = \Mockery::mock('WP_Query');

        $collector = \Mockery::mock(ContextCollectorInterface::class);
        $collector->shouldReceive('accept')->once()->with($query)->andReturn(false);
        $collector->shouldReceive('provide')->never();

        assertSame([], WpContextLoader::load($query, $collector));
    }

    public function testLoad()
    {
        $query = \Mockery::mock('WP_Query');
        $query->shouldReceive('is_archive')->zeroOrMoreTimes()->andReturn(false);
        $query->shouldReceive('is_singular')->zeroOrMoreTimes()->andReturn(true);
        $query->shouldReceive('is_post')->zeroOrMoreTimes()->andReturn(true);
        $query->shouldReceive('is_page')->zeroOrMoreTimes()->andReturn(false);

        $archive = \Mockery::mock(ContextProviderInterface::class);
        $page = clone $archive;
        $singular = clone $page;
        $post = \Mockery::mock(UpdatableContextProviderInterface::class);

        $archive
            ->shouldReceive('accept')
            ->once()
            ->with($query)
            ->andReturnUsing(function (\WP_Query $query) {
                return $query->is_archive();
            });

        $archive
            ->shouldReceive('provide')
            ->zeroOrMoreTimes()
            ->andReturn([
                'archive'     => 'archive',
                'description' => 'This is an archive',
                'post_ids'    => [1, 2, 3, 4, 5]
            ]);

        $page
            ->shouldReceive('accept')
            ->once()
            ->with($query)
            ->andReturnUsing(function (\WP_Query $query) {
                return $query->is_page();
            });

        $page
            ->shouldReceive('provide')
            ->zeroOrMoreTimes()
            ->andReturn([
                'page'        => 'page',
                'description' => 'This is a page',
                'post_ids'    => [4, 5, 6, 7, 8]
            ]);

        $singular
            ->shouldReceive('accept')
            ->once()
            ->with($query)
            ->andReturnUsing(function (\WP_Query $query) {
                return $query->is_singular();
            });

        $singular
            ->shouldReceive('provide')
            ->zeroOrMoreTimes()
            ->andReturn([
                'singular'    => 'singular',
                'description' => 'This is singular',
                'post_ids'    => [7, 8, 9, 10],
                'meh'         => 'I will never seen the light'
            ]);

        $post
            ->shouldReceive('accept')
            ->once()
            ->with($query)
            ->andReturnUsing(function (\WP_Query $query) {
                return $query->is_post();
            });

        $post
            ->shouldReceive('provide')
            ->zeroOrMoreTimes()
            ->andReturn([
                'post'        => 'post',
                'description' => 'This is a post',
                'post_ids'    => [9, 10, 11, 12]
            ]);

        $post
            ->shouldReceive('update')
            ->zeroOrMoreTimes()
            ->andReturnUsing(function (array $context) {

                assertArrayHasKey('meh', $context);
                unset($context['meh']);

                return $context;
            });

        Actions::expectFired('brain.context.providers')
            ->once()
            ->whenHappen(
                function (ArrayMergeContextCollector $C) use ($archive, $page, $singular, $post) {
                    $C
                        ->addProvider($archive)
                        ->addProvider($page)
                        ->addProvider($singular)
                        ->addProvider($post);
                }
            );

        Filters::expectApplied('brain.context.context')
            ->once()
            ->andReturnUsing(function (array $context, \WP_Query $query) {
                $context['description'] .= '!!!';

                return $context;
            });

        $expected = [
            'post'        => 'post',
            'singular'    => 'singular',
            'description' => 'This is a post!!!',
            'post_ids'    => [9, 10, 11, 12]
        ];

        assertEquals($expected, WpContextLoader::load($query));
    }
}
