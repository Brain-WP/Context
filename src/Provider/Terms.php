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
class Terms extends ByQueryCallback
{
    /**
     * @param array $queryArgs
     * @return string
     */
    protected function defaultKey(array $queryArgs): string
    {
        $taxonomy = array_key_exists('taxonomy', $queryArgs) ? $queryArgs['taxonomy'] : null;
        if ($taxonomy && is_string($taxonomy) && substr_count($taxonomy, ',')) {
            $taxonomy = array_map('trim', explode(',', $taxonomy));
        }

        if ($taxonomy && is_array($taxonomy)) {
            $taxonomies = [];
            foreach ($taxonomy as $aTaxonomy) {
                ($aTaxonomy && is_string($aTaxonomy)) and $taxonomies[$aTaxonomy] = 1;
            }
            $taxonomyNames = array_keys($taxonomies);
            sort($taxonomyNames, SORT_STRING);
            $taxonomy = implode('_', $taxonomyNames);
        }

        return ($taxonomy && is_string($taxonomy)) ? "{$taxonomy}_terms" : 'terms';
    }

    /**
     * @param array $queryArgs
     * @return callable
     */
    protected function buildCallback(array $queryArgs): callable
    {
        return static function () use ($queryArgs): array {
            $terms = get_terms($queryArgs);

            return is_array($terms) ? $terms : [];
        };
    }
}
