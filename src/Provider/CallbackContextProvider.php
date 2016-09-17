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

    use CallbackAcceptTrait;

    /**
     * @var callable
     */
    private $provider;

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
     * @return array
     */
    public function provide()
    {
        $provider = $this->provider;
        $context = $provider();

        return is_array($context) ? $context : [];
    }
}
