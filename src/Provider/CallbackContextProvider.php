<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the Context package.
 *
 * (c) Giuseppe Mazzapica
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brain\Context\Provider;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context
 * @license http://opensource.org/licenses/MIT MIT
 */
final class CallbackContextProvider implements ContextProviderInterface
{

    /**
     * @var callable
     */
    private $provider;

    /**
     * @var \WP_Query
     */
    private $query;

    /**
     * @var callable
     */
    private $acceptCallback = '__return_true';

    /**
     * @param callable $provider
     * @param callable $acceptCallback
     */
    public function __construct(callable $provider, callable $acceptCallback = null)
    {
        $this->provider = $provider;
        $acceptCallback and $this->acceptCallback = $acceptCallback;
    }

    /**
     * @param \WP_Query $query
     * @return bool
     */
    public function accept(\WP_Query $query)
    {
        $callback = $this->acceptCallback;
        $accept = $callback($query);
        $this->query = $query;

        return (bool)filter_var($accept, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return array
     */
    public function provide()
    {
        if (!$this->query instanceof \WP_Query) {
            return [];
        }

        $provider = $this->provider;
        $context = $provider($this->query);

        return is_array($context) ? $context : [];
    }
}
