<?php

declare(strict_types=1);

namespace Brain\Context\Provider;

use Brain\Context\Provider;
use Psr\Log\LoggerInterface;

abstract class BaseEntitiesFlattener implements Provider
{
    /**
     * @var Provider
     */
    private $provider;

    /**
     * @param Provider $provider
     */
    final public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @param \WP_Query $query
     * @param LoggerInterface $logger
     * @return array|null
     */
    final public function provide(\WP_Query $query, LoggerInterface $logger): ?array
    {
        $context = $this->provider->provide($query, $logger);

        return ($context === null) ? null : (array)$this->processItem($context);
    }

    /**
     * @param mixed $item
     * @return bool
     *
     * @psalm-assert-if-true \WP_Post|\WP_Comment|\WP_Term|\WP_User $item
     */
    final protected function isWpEntity($item): bool
    {
        return
            $item instanceof \WP_Post
            || $item instanceof \WP_Comment
            || $item instanceof \WP_Term
            || $item instanceof \WP_User;
    }

    /**
     * @param mixed $item
     * @return mixed
     */
    abstract protected function processItem($item);
}
