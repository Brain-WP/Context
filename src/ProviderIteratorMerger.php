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

use Brain\Context\Provider\ContextProviderInterface;
use Brain\Context\Provider\UpdatableContextProviderInterface;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context
 * @license http://opensource.org/licenses/MIT MIT
 */
class ProviderIteratorMerger
{

    /**
     * @var \WP_Query
     */
    private $query;

    /**
     * QueryContextArrayMerger constructor.
     * @param \WP_Query $query
     */
    public function __construct(\WP_Query $query)
    {
        $this->query = $query;
    }

    /**
     * @param \Iterator $providers
     * @return array
     */
    public function merge(\Iterator $providers)
    {
        return $this->doMerge($providers, 'array_merge');
    }

    /**
     * @param \Iterator $providers
     * @return array
     */
    public function mergeRecursive(\Iterator $providers)
    {
        return $this->doMerge($providers, 'array_merge_recursive');
    }

    /**
     * @param \Iterator $providers
     * @param callable $merger
     * @return array
     */
    private function doMerge(\Iterator $providers, callable $merger)
    {
        $merged = [];
        $providers->rewind();
        while ($providers->valid()) {
            /** @var ContextProviderInterface|UpdatableContextProviderInterface $provider */
            $provider = $providers->current();
            if (!$provider instanceof ContextProviderInterface || !$provider->accept($this->query)) {
                $providers->next();
                continue;
            }

            $merged = $merger($merged, $provider->provide());
            if ($provider instanceof UpdatableContextProviderInterface) {
                $merged = $provider->update($merged);
            }

            $providers->next();
        }

        return $merged;
    }
}
