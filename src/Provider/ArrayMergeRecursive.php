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

use Brain\Context;
use Brain\Context\Provider;
use Psr\Log\LoggerInterface;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context
 * @license http://opensource.org/licenses/MIT MIT
 */
final class ArrayMergeRecursive implements Provider
{
    /**
     * @var ProviderMerger
     */
    private $merger;

    /**
     * @param callable|null $queryPredicate
     * @return ArrayMergeRecursive
     */
    public static function new(?callable $queryPredicate = null): ArrayMergeRecursive
    {
        return new self(ProviderMerger::newRecursive($queryPredicate));
    }

    /**
     * @param ProviderMerger $merger
     */
    private function __construct(ProviderMerger $merger)
    {
        $this->merger = $merger;
    }

    /**
     * @param Provider $provider
     * @return static
     */
    public function addProvider(Provider $provider): ArrayMergeRecursive
    {
        $this->merger->add($provider);

        return $this;
    }

    /**
     * @param \WP_Query $query
     * @param LoggerInterface $logger
     * @return array|null
     */
    public function provide(\WP_Query $query, LoggerInterface $logger): ?array
    {
        return $this->merger->merge($query, $logger);
    }
}
