<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the Context package.
 *
 * (c) Giuseppe Mazzapica
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brain\Context;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @property \SplQueue $providers
 * @property \WP_Query $query
 */
final class ArrayMergeContextCollector implements ContextCollectorInterface
{
    use ContextCollectorTrait;

    /**
     * @return array
     */
    public function provide()
    {
        if (!$this->query instanceof \WP_Query) {
            return [];
        }

        $merger = new QueryContextIteratorMerger($this->query);

        return $merger->merge($this->providers);
    }
}
