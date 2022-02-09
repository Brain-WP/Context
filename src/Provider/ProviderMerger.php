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
final class ProviderMerger
{
    private const MODE_SIMPLE = 'simple';
    private const MODE_RECURSIVE = 'recursive';

    /**
     * @var string
     */
    private $mode;

    /**
     * @var list<Provider>
     */
    private $providers = [];

    /**
     * @var callable|null
     */
    private $queryPredicate;

    /**
     * @param callable|null $queryPredicate
     * @return ProviderMerger
     */
    public static function newSimple(?callable $queryPredicate = null): ProviderMerger
    {
        return new self(self::MODE_SIMPLE, $queryPredicate);
    }

    /**
     * @param callable|null $queryPredicate
     * @return ProviderMerger
     */
    public static function newRecursive(?callable $queryPredicate = null): ProviderMerger
    {
        return new self(self::MODE_RECURSIVE, $queryPredicate);
    }

    /**
     * @param string $mode
     * @param callable|null $queryPredicate
     */
    private function __construct(string $mode, ?callable $queryPredicate = null)
    {
        $this->mode = $mode;
        $this->queryPredicate = $queryPredicate;
    }

    /**
     * @param Provider $provider
     * @return void
     */
    public function add(Provider $provider): void
    {
        $this->providers[] = $provider;
    }

    /**
     * @param \WP_Query $query
     * @param LoggerInterface $logger
     * @return array|null
     */
    public function merge(\WP_Query $query, LoggerInterface $logger): ?array
    {
        if ($this->queryPredicate && !($this->queryPredicate)($query)) {
            return null;
        }

        $context = null;
        foreach ($this->providers as $provider) {
            $data = $provider->provide($query, $logger);
            if ($data === null) {
                continue;
            }
            ($context === null) and $context = [];
            $context = ($this->mode === self::MODE_RECURSIVE)
                ? array_merge_recursive($context, $data)
                : array_merge($context, $data);
        }

        return $context;
    }
}
