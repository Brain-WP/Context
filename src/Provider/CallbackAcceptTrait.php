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
 *
 * @property callable $acceptCallback
 */
trait CallbackAcceptTrait
{

    /**
     * @param \WP_Query $query
     * @return bool
     *
     * @see ContextProviderInterface::accept()
     */
    public function accept(\WP_Query $query)
    {
        $callback = $this->acceptCallback;
        $accept = $callback($query);

        return (bool)filter_var($accept, FILTER_VALIDATE_BOOLEAN);
    }
}
