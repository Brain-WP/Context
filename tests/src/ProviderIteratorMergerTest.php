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

use Brain\Context\Provider\ContextProviderInterface;
use Brain\Context\Provider\UpdatableContextProviderInterface;
use Brain\Context\ProviderIteratorMerger;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context
 * @license http://opensource.org/licenses/MIT MIT
 */
class ProviderIteratorMergerTest extends TestCase
{

    private function getIterator(\WP_Query $query)
    {
        $archive = \Mockery::mock(ContextProviderInterface::class);
        $search = \Mockery::mock(UpdatableContextProviderInterface::class);
        $singular = clone $archive;
        $page = clone $search;

        $is = function ($method) use ($query) {
            return function () use ($query, $method) {
                return call_user_func([$query, "is_{$method}"]);
            };
        };

        $archive->shouldReceive('accept')->with($query)->andReturnUsing($is('archive'));
        $search->shouldReceive('accept')->with($query)->andReturnUsing($is('search'));
        $singular->shouldReceive('accept')->with($query)->andReturnUsing($is('singular'));
        $page->shouldReceive('accept')->with($query)->andReturnUsing($is('page'));

        $archive->shouldReceive('provide')->andReturn(['id' => 'ar', 'n' => [1], 'archive' => 1]);
        $search->shouldReceive('provide')->andReturn(['id' => 'se', 'n' => [2], 'search' => 1]);
        $singular->shouldReceive('provide')->andReturn(['id' => 'si', 'n' => [3], 'singular' => 1]);
        $page->shouldReceive('provide')->andReturn(['id' => 'pa', 'n' => [4], 'page' => 1]);

        $update = function (array $context) {
            return array_merge($context, ['updated' => true]);
        };

        $search->shouldReceive('update')->andReturnUsing($update);
        $page->shouldReceive('update')->andReturnUsing($update);

        return new \ArrayIterator(compact('archive', 'search', 'singular', 'page'));
    }

    public function testMerge()
    {
        $query = \Mockery::mock('WP_Query');
        $query->shouldReceive('is_archive')->andReturn(true);
        $query->shouldReceive('is_search')->andReturn(true);
        $query->shouldReceive('is_singular')->andReturn(false);
        $query->shouldReceive('is_page')->andReturn(false);

        $merger = new ProviderIteratorMerger($query);

        $expected = [
            'id'      => 'se',
            'n'       => [2],
            'archive' => 1,
            'search'  => 1,
            'updated' => true
        ];

        assertEquals($expected, $merger->merge($this->getIterator($query)));
    }

    public function testMergeRecursive()
    {
        $query = \Mockery::mock('WP_Query');
        $query->shouldReceive('is_archive')->andReturn(false);
        $query->shouldReceive('is_search')->andReturn(false);
        $query->shouldReceive('is_singular')->andReturn(true);
        $query->shouldReceive('is_page')->andReturn(true);

        $merger = new ProviderIteratorMerger($query);

        $expected = [
            'id'       => ['si', 'pa'],
            'n'        => [3, 4],
            'singular' => 1,
            'page'     => 1,
            'updated'  => true
        ];

        assertEquals($expected, $merger->mergeRecursive($this->getIterator($query)));
    }
}
