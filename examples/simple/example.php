<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the Context package.
 *
 * (c) Giuseppe Mazzapica
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This file is an example on how to integrate Context in a very basic way in WordPress workflow.
 * We save the obtained context in a global variable, that's not really recommendable, but helps
 * to understand how the library works.
 *
 * In the folder containing this file there are two simple implementation of context provider.
 * In real world you probably have more providers...
 *
 * This file would ideally required from a theme `functions.php`, or even placed as MU plugin...
 */

/**
 * NOTE: I'm assuming that an autoloader (Composer?) is in place.
 */

namespace Brain\Context\Examples\Simple;

use Brain\Context\WpContextLoader;
use Brain\Context\ArrayMergeContextCollector;

// Let's add all our providers, the collector will pull context from them
// when the query fits.
add_action('brain.context.providers', function (ArrayMergeContextCollector $collector) {

    $collector
        ->addProvider(new HomePageContext())
        ->addProvider(new GalleryContext());

});

// On `template_redirect` we store the template context in a global variable
// so we can use it in templates...
add_action('template_redirect', function () {

    global $wp_query, $template_context;

    $template_context = WpContextLoader::load($wp_query);

});