<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the Context package.
 *
 * (c) Giuseppe Mazzapica
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brain\Context\Examples\HierarchyMustache\Loader;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context
 * @license http://opensource.org/licenses/MIT MIT
 */

use Brain\Hierarchy\Loader\TemplateLoaderInterface;
use Mustache_Engine;

class MustacheTemplateLoader implements TemplateLoaderInterface
{

    /**
     * @var \Mustache_Engine
     */
    private $engine;

    /**
     * @var array
     */
    private $data;

    public function __construct( \Mustache_Engine $engine, array $data = [] )
    {
        $this->engine = $engine;
        $this->data = $data;
    }

    /**
     * @param string $templatePath
     * @return mixed
     */
    public function load( $templatePath )
    {
        $data = apply_filters( 'mustache_template_context', $this->data, $templatePath);
        $template = file_get_contents( $templatePath );

        return $this->engine->render( $template, $data );
    }
}