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
abstract class ByQueryCallback implements Provider
{
    /**
     * @var string|null
     */
    protected $key;

    /**
     * @var array
     */
    protected $queryArgs;

    /**
     * @param array $queryArgs
     * @param string|null $key
     */
    public function __construct(array $queryArgs, ?string $key = null)
    {
        $this->key = $key;
        $this->queryArgs = $queryArgs;
    }

    /**
     * @param \WP_Query $query
     * @param LoggerInterface $logger
     * @return array|null
     */
    public function provide(\WP_Query $query, LoggerInterface $logger): ?array
    {
        try {
            $provider = new ByCallback($this->buildCallback($this->queryArgs));
            $context = $provider->provide($query, $logger);
        } catch (\Throwable $exception) {
            $logger->error($exception->getMessage(), compact('exception'));
            $context = null;
        }

        if ($context === null) {
            return null;
        }

        $key = $this->key ?? $this->defaultKey($this->queryArgs);

        return [$key => $context];
    }

    /**
     * @param array $queryArgs
     * @return string
     */
    abstract protected function defaultKey(array $queryArgs): string;

    /**
     * @param array $queryArgs
     * @return callable
     */
    abstract protected function buildCallback(array $queryArgs): callable;
}
