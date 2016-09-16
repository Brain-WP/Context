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
final class UsersContextProvider implements ContextProviderInterface
{

    use CallbackAcceptTrait;

    /**
     * @var string
     */
    private $key;

    /**
     * @var array
     */
    private $queryArgs;

    /**
     * @var callable
     */
    private $acceptCallback = '__return_true';

    /**
     * @param string $key
     * @param array $queryArgs
     * @param callable $acceptCallback
     */
    public function __construct(
        $key,
        array $queryArgs = [],
        callable $acceptCallback = '__return_true'
    ) {
        $this->key = $key;
        $this->queryArgs = $queryArgs;
        $this->acceptCallback = $acceptCallback;
    }

    /**
     * @return array
     */
    public function provide()
    {
        return [$this->key => get_users($this->queryArgs)];
    }
}