<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the Context package.
 *
 * (c) Giuseppe Mazzapica
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brain\Context\Collector;

use Brain\Context\Provider\ContextProviderInterface;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context
 * @license http://opensource.org/licenses/MIT MIT
 */
trait ContextCollectorTrait
{

    /**
     * @var \SplQueue
     */
    private $providers;

    /**
     * @var \WP_Query
     */
    private $query;

    private $acceptCallback = '__return_true';

    public function __construct(callable $acceptCallback = null)
    {
        $this->providers = new \SplQueue();
        $acceptCallback and $this->acceptCallback = $acceptCallback;
    }

    /**
     * @param \Brain\Context\Provider\ContextProviderInterface $provider
     * @return \Brain\Context\Collector\ContextCollectorInterface
     */
    public function addProvider(ContextProviderInterface $provider)
    {
        /** @var $this ContextCollectorInterface */

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

        $acceptCallback = $this->acceptCallback;

        return $acceptCallback($query);
    }
}
