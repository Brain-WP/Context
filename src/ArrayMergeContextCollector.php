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
final class ArrayMergeContextCollector implements ContextCollectorInterface
{

    /**
     * @var \SplQueue
     */
    private $providers;

    /**
     * @var \WP_Query
     */
    private $query;

    public function __construct()
    {
        $this->providers = new \SplQueue();
    }

    /**
     * @param ContextProviderInterface $provider
     * @return \Brain\Context\ContextCollectorInterface
     */
    public function addProvider(ContextProviderInterface $provider)
    {
        $this->providers->enqueue($provider);

        // By using this function it is possible to remove the just-added provider
        // by calling `SplQueue::pop()` on the passed providers object
        do_action('brain.context.added', $provider, $this->providers);

        return $this;
    }

    /**
     * @param \WP_Query $query
     * @return bool
     */
    public function accept(\WP_Query $query)
    {
        $this->query = $query;

        return true;
    }

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

            $context = array_merge($context, $provider->provide());
            if ($provider instanceof UpdatableContextProviderInterface) {
                $context = $provider->update($context);
            }
        }

        return $context;
    }
}