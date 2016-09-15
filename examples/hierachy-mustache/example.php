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
 * This file is an example on how to integrate Context in a quite advanced WordPress workflow.
 *
 * This example has some requirements/assumptions:
 *  - an autoloader (Composer?) is in place
 *  - there's Brain\Hierarchy package available (https://github.com/Brain-WP/Hierarchy)
 *  - there's PHP Mustache package available (https://github.com/bobthecow/mustache.php)
 *
 * In the "Providers" folder there are two simple implementations of context provider.
 * In real world you will probably have more providers.
 * My suggestion is to use more specialized providers than few big providers.
 *
 * In the "Loader" folder there is a custom Brain\Hierarchy template loader implementation that
 * loads and renders mustache templates.
 *
 * This file would ideally required from a theme `functions.php`, or even placed as MU plugin...
 */

namespace Brain\Context\Examples\HierarchyMustache;

use Brain\Context\Examples\HierarchyMustache\Loader\MustacheTemplateLoader;
use Brain\Context\WpContextLoader;
use Brain\Context\WpTemplateContextCollector;
use Brain\Hierarchy\Finder\SubfolderTemplateFinder;
use Brain\Hierarchy\QueryTemplate;

// Let's add all our providers, the collector will pull context from them when the query fits.
add_action('brain.context.providers', function (WpTemplateContextCollector $collector) {

    $collector
        ->addProvider(new Providers\HomePageContext())
        ->addProvider(new Providers\GalleryContext());

});

// All the workflow needs a query object available, `template_redirect` is perfect place
add_action('template_redirect', function () {

    global $wp_query;

    // Will look for "*.mustache" templates in "/templates" subfolder of theme / child theme
    $finder = new SubfolderTemplateFinder('templates', 'mustache');

    /*
     * Load context from our providers.
     * This context maybe be filtered using:
     *  - `brain.context.context`, from `WpContextLoader`,
     *     which will pass the query as additional hook argument
     *  - `mustache_template_context`, from `MustacheTemplateLoader`,
     *     which will pass the template path as additional hook argument
     */
    $context = WpContextLoader::load($wp_query);

    // Create an instance of our mustache template loader
    $loader = new MustacheTemplateLoader(new \Mustache_Engine(), $context);

    // ...and an instance of Brain\Hierarchy QueryTemplate
    $queryTemplate = new QueryTemplate($finder, $loader);

    // 3rd param of loadTemplate(), passed by reference, it is set to true if template is found
    $found = false;

    // Load & render template (if found)
    $content = $queryTemplate->loadTemplate($wp_query, true, $found);

    // If template was found, let's output it and exit, otherwise WordPress will continue its work
    if ($found) {
        echo $content;
        exit();
    }

}, 1);