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
final class ArrayContextProvider implements ContextProviderInterface
{

    /**
     * @var array
     */
    private $context = [];

    /**
     * @var callable
     */
    private $acceptCallback = '__return_true';

    /**
     * @param array $context
     * @param callable $acceptCallback
     */
    public function __construct(array $context = [], callable $acceptCallback = '__return_true')
    {
        $this->context = $context;
        $this->acceptCallback = $acceptCallback;
    }

    /**
     * @param \WP_Query $query
     * @return bool
     */
    public function accept(\WP_Query $query)
    {
        $callback = $this->acceptCallback;
        $accept = $callback($query);

        return (bool)filter_var($accept, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return array
     */
    public function provide()
    {
        return $this->context;
    }
}