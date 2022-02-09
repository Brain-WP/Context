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

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context
 * @license http://opensource.org/licenses/MIT MIT
 */
class Posts extends ByQueryCallback
{
    /**
     * @param array $queryArgs
     * @return string
     */
    protected function defaultKey(array $queryArgs): string
    {
        return 'posts';
    }

    /**
     * @param array $queryArgs
     * @return callable
     */
    protected function buildCallback(array $queryArgs): callable
    {
        return static function () use ($queryArgs): array {
            /** @psalm-suppress MixedArgumentTypeCoercion */
            $posts = get_posts($queryArgs);

            return is_array($posts) ? $posts : [];
        };
    }
}
