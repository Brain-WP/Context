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

use Brain\Context\Provider\WpEntitiesArraizer;
use Brain\Context\Provider\WpEntitiesFlattener;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Context implements LoggerAwareInterface
{
    public const ACTION_INIT_PROVIDERS = 'brain.context.providers';
    public const ACTION_INIT_LOGGER = 'brain.context.logger';
    public const ACTION_BEFORE_CONTEXT = 'brain.context.before-context';
    public const ACTION_PROVIDER_ADDED = 'brain.context.added';
    public const FILTER_GLOBAL_KEYS = 'brain.context.forwarded-globals';

    private const GLOBAL_KEYS = [
        'posts',
        'post',
        'wp_did_header',
        'wp_query',
        'wp_rewrite',
        'wpdb',
        'wp_version',
        'wp',
        'id',
        'comment',
        'user_ID',
    ];

    /**
     * @var bool
     */
    private $provided = false;

    /**
     * @var bool
     */
    private $providing = false;

    /**
     * @var LoggerInterface|null
     */
    private $logger = null;

    /**
     * @var class-string<WpEntitiesArraizer>|class-string<WpEntitiesFlattener>|null
     */
    private $processor = null;

    /**
     * @var list<array{Provider|ProviderFactory, callable|null, bool}>
     */
    private $providers = [];

    /**
     * @var bool
     */
    private $forwardGlobals = false;

    /**
     * @return Context
     */
    public static function new(): Context
    {
        return new self();
    }

    /**
     */
    private function __construct()
    {
    }

    /**
     * @param Provider $provider
     * @param callable|null $queryPredicate
     * @return static
     */
    public function withProvider(Provider $provider, ?callable $queryPredicate = null): Context
    {
        if (!$this->isExtendingPossible(sprintf('add provider %s', get_class($provider)))) {
            return $this;
        }

        $this->providers[] = [$provider, $queryPredicate, false];
        do_action(self::ACTION_PROVIDER_ADDED, $provider, $queryPredicate);

        return $this;
    }

    /**
     * @param ProviderFactory $factory
     * @param callable|null $queryPredicate
     * @return static
     */
    public function withProviderFactory(
        ProviderFactory $factory,
        ?callable $queryPredicate = null
    ): Context {

        if (!$this->isExtendingPossible(sprintf('add provider factory %s', get_class($factory)))) {
            return $this;
        }

        $this->providers[] = [$factory, $queryPredicate, true];

        return $this;
    }

    /**
     * @return static
     */
    public function convertEntitiesToArrays(): Context
    {
        if ($this->isExtendingPossible('configure entities as array conversion')) {
            $this->processor = Provider\WpEntitiesArraizer::class;
        }

        return $this;
    }

    /**
     * @return static
     */
    public function convertEntitiesToPlainObjects(): Context
    {
        if ($this->isExtendingPossible('configure entities as plain objects conversion')) {
            $this->processor = Provider\WpEntitiesFlattener::class;
        }

        return $this;
    }

    /**
     * @return static
     */
    public function forwardGlobals(): Context
    {
        if ($this->isExtendingPossible('configure globals forwarding')) {
            $this->forwardGlobals = true;
        }

        return $this;
    }

    /**
     * @param \WP_Query|null $query
     * @return array|null
     */
    public function provide(?\WP_Query $query = null): ?array
    {
        $query = $this->initialize($query);
        if (!$query) {
            $this->provided = false;

            return null;
        }

        $logger = $this->logger ?? new NullLogger();
        $provider = $this->buildProvider($query, $logger);

        do_action(self::ACTION_BEFORE_CONTEXT, $query);

        $result = $provider->provide($query, $logger);
        $this->provided = false;

        return $result;
    }

    /**
     * @param LoggerInterface $logger
     * @return void
     *
     * phpcs:disable Inpsyde.CodeQuality.NoAccessors
     */
    public function setLogger(LoggerInterface $logger): void
    {
        // phpcs:enable Inpsyde.CodeQuality.NoAccessors
        $this->logger = $logger;
    }

    /**
     * @param \WP_Query|null $query
     * @return \WP_Query|null
     */
    private function initialize(?\WP_Query $query): ?\WP_Query
    {
        // Prevent endless recursion if provide() is called during ACTION_INIT_PROVIDERS action.
        if ($this->providing) {
            return null;
        }

        $setLogger = function (LoggerInterface $logger): void {
            $this->setLogger($logger);
        };

        do_action(self::ACTION_INIT_LOGGER, $setLogger);

        $this->providing = true;
        do_action(self::ACTION_INIT_PROVIDERS, $this);
        $this->providing = false;

        $this->provided = true;

        if (!$this->providers) {
            return null;
        }

        if (!$query) {
            /** @psalm-suppress InvalidGlobal */
            global $wp_query;
            if (!($wp_query instanceof \WP_Query)) {
                return null;
            }

            $query = $wp_query;
        }

        return $query;
    }

    /**
     * @param \WP_Query $query
     * @param LoggerInterface $logger
     * @return Provider
     */
    private function buildProvider(\WP_Query $query, LoggerInterface $logger): Provider
    {
        $collector = Provider\ArrayMerge::new();

        foreach ($this->providers as [$provider, $predicate, $isFactory]) {
            if ($predicate && !$predicate($query)) {
                continue;
            }

            if ($isFactory && ($provider instanceof ProviderFactory)) {
                $provider = $provider->create($query, $logger);
                $provider and do_action(self::ACTION_PROVIDER_ADDED, $provider, $predicate);
            }

            if ($provider instanceof Provider) {
                $collector->addProvider($provider);
            }
        }

        if ($this->forwardGlobals) {
            $collector->addProvider(new Provider\ByArray($this->extractGlobals()));
        }

        return $this->maybeWrapWithProcessor($collector);
    }

    /**
     * @param string $subject
     * @return bool
     */
    private function isExtendingPossible(string $subject): bool
    {
        if (!$this->provided) {
            return true;
        }

        if (!$this->logger) {
            return false;
        }

        $this->logger->error(
            sprintf(
                'Can not %s when already provided. Please use %s hook to add provider.',
                $subject,
                self::ACTION_INIT_PROVIDERS
            )
        );

        return false;
    }

    /**
     * @return array<string, mixed>
     */
    private function extractGlobals(): array
    {
        $keys = apply_filters(self::FILTER_GLOBAL_KEYS, self::GLOBAL_KEYS);
        if (!is_array($keys)) {
            return [];
        }

        $extracted = [];
        foreach ($keys as $key) {
            if (is_string($key) && array_key_exists($key, $GLOBALS)) {
                $extracted[$key] = $GLOBALS[$key];
            }
        }

        return $extracted;
    }

    /**
     * @param Provider $provider
     * @return Provider
     */
    private function maybeWrapWithProcessor(Provider $provider): Provider
    {
        switch (true) {
            case ($this->processor === WpEntitiesFlattener::class):
                $provider = new WpEntitiesFlattener($provider);
                break;
            case ($this->processor === WpEntitiesArraizer::class):
                $provider = new WpEntitiesArraizer($provider);
                break;
        }

        return $provider;
    }
}
