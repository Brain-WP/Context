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
final class ByCallback implements Provider
{
    /**
     * @var callable
     */
    private $provider;

    /**
     * @param callable $provider
     */
    public function __construct(callable $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @param \WP_Query $query
     * @param LoggerInterface $logger
     * @return array|null
     */
    public function provide(\WP_Query $query, LoggerInterface $logger): ?array
    {
        try {
            $context = ($this->provider)($query, $logger);
            if (($context !== null) && !is_array($context)) {
                $type = is_object($context) ? get_class($context) : gettype($context);
                $logger->warning("Provider callback returned unexpected type: {$type}.");
                $context = null;
            }

            return $context;
        } catch (\Throwable $exception) {
            $logger->error($exception->getMessage(), compact('exception'));

            return null;
        }
    }
}
