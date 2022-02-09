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

namespace Brain\Context\Provider;

use Brain\Context\Provider;
use Psr\Log\LoggerInterface;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context
 * @license http://opensource.org/licenses/MIT MIT
 */
final class ByArray implements Provider
{
    /**
     * @var array
     */
    private $context;

    /**
     * @param array $context
     */
    public function __construct(array $context = [])
    {
        $this->context = $context;
    }

    /**
     * @return array
     */
    public function provide(\WP_Query $query, LoggerInterface $logger): ?array
    {
        return $this->context;
    }
}
