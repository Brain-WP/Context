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

use Brain\Hierarchy\Loader\TemplateLoaderInterface;
use Mustache_Engine;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context
 * @subpackage Examples
 * @license http://opensource.org/licenses/MIT MIT
 */
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

    /**
     * MustacheTemplateLoader constructor.
     * @param \Mustache_Engine $engine
     * @param array $data
     */
    public function __construct( \Mustache_Engine $engine, array $data = [] )
    {
        $this->engine = $engine;
        $this->data = $data;
    }

    /**
     * @param string $templatePath
     * @return string
     */
    public function load( $templatePath )
    {
        $data = apply_filters( 'mustache_template_context', $this->data, $templatePath);
        $template = file_get_contents( $templatePath );

        return $this->engine->render( $template, $data );
    }
}