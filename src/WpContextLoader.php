<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the Context package.
 *
 * (c) Giuseppe Mazzapica
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brain\Context;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context
 * @license http://opensource.org/licenses/MIT MIT
 */
class WpContextLoader
{

    /**
     * Convenience method to load a typical WordPress workflow with Context.
     * On a hook that makes a query available, e.g. `template_redirect`, if you target main query,
     * you can call this method and just obtain back the context array to pass to templates.
     *
     * Of course, you need to create classes implementing `ContextProviderInterface` and add them
     * to collector using `brain.context.providers` hook.
     *
     * @param \WP_Query $query
     * @param ContextCollectorInterface $collector
     * @return array
     */
    public static function load(\WP_Query $query, ContextCollectorInterface $collector = null)
    {
        $collector or $collector = new ArrayMergeContextCollector();

        if (!$collector->accept($query)) {
            return [];
        }

        // Use this hook to add context provider calling `addProvider()` method on the
        // passed collector object
        do_action('brain.context.providers', $collector);

        $context = $collector->provide();

        // just in time context editing?
        $context = apply_filters('brain.context.context', $context, $query);
        is_array($context) or $context = [];

        // some cleanup
        unset($collector);

        return $context;
    }
}
