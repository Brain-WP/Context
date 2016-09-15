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
 */
final class ArrayMergeRecursiveContextCollector implements ContextCollectorInterface
{

    use ArrayMergeContextCollectorTrait;

    /**
     * @return array
     */
    public function provide()
    {
        if (!$this->query instanceof \WP_Query) {
            return [];
        }

        $context = [];
        while (!$this->providers->isEmpty()) {
            /** @var ContextProviderInterface|UpdatableContextProviderInterface $provider */
            $provider = $this->providers->dequeue();
            if (!$provider->accept($this->query)) {
                continue;
            }

            $context = array_merge_recursive($context, $provider->provide());
            if ($provider instanceof UpdatableContextProviderInterface) {
                $context = $provider->update($context);
            }
        }

        return $context;
    }
}