<?php

/*
 * This file is part of the Context package.
 *
 * (c) Giuseppe Mazzapica
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Brain\Context;

use Psr\Log\LoggerInterface;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context
 * @license http://opensource.org/licenses/MIT MIT
 */
interface ProviderFactory
{
    /**
     * @param \WP_Query $query
     * @param LoggerInterface $logger
     * @return Provider|null
     */
    public function create(\WP_Query $query, LoggerInterface $logger): ?Provider;
}
