<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the Context package.
 *
 * (c) Giuseppe Mazzapica
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brain\Context\Examples\Simple;

use Brain\Context\ContextProviderInterface;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context
 * @subpackage Examples
 * @license http://opensource.org/licenses/MIT MIT
 */
class HomePageContext implements ContextProviderInterface
{

    /**
     * @param \WP_Query $query
     * @return bool
     */
    public function accept(\WP_Query $query)
    {
        return $query->is_front_page();
    }

    /**
     * @return array
     */
    public function provide()
    {
        return [
            'welcome'       => 'Hi, welcome to my awesome website!',
            'register_page' => $this->registerPage(),
            'in_evidence'   => $this->inEvidence(),
        ];
    }

    /**
     * @return null|\WP_Post
     */
    private function registerPage()
    {
        $query = new \WP_Query([
            'post_type'      => 'page',
            'posts_per_page' => 1,
            'meta_query'     => [
                [
                    'key'   => '_wp_page_template',
                    'value' => 'register.php'
                ]
            ]
        ]);

        return $query->have_posts() ? $query->post : null;
    }

    /**
     * @return \WP_Query
     */
    private function inEvidence()
    {
        return new \WP_Query([
            'post_type'      => 'post',
            'posts_per_page' => 5,
            'orderby'        => 'post_date',
            'order'          => 'DESC',
            'meta_query'     => [
                [
                    'key'   => 'in_evidence',
                    'value' => '1'
                ]
            ]
        ]);
    }
}
