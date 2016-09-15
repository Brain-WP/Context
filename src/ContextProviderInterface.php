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
interface ContextProviderInterface
{

    /**
     * @param \WP_Query $query
     * @return bool
     */
    public function accept(\WP_Query $query);

    /**
     * @return array
     */
    public function provide();
}
