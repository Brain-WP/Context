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

interface ContextCollectorInterface extends ContextProviderInterface
{

    /**
     * @param \Brain\Context\Provider\ContextProviderInterface $provider
     * @return \Brain\Context\Collector\ContextCollectorInterface
     */
    public function addProvider(ContextProviderInterface $provider);
}
