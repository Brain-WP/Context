<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the Context package.
 *
 * (c) Giuseppe Mazzapica
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brain\Context\Examples\HierarchyMustache\Providers;

use Brain\Context\ContextProviderInterface;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context
 * @subpackage Examples
 * @license http://opensource.org/licenses/MIT MIT
 */
class GalleryContext implements ContextProviderInterface
{

    /**
     * @param \WP_Query $query
     * @return bool
     */
    public function accept(\WP_Query $query)
    {
        return $query->is_front_page() || $query->is_singular(['post', 'page']);
    }

    /**
     * @return array
     */
    public function provide()
    {
        $gallery_query = new \WP_Query([
            'post_type'      => 'attachment',
            'posts_per_page' => 10,
            'tax_query'      => [
                [
                    'taxonomy' => 'gallery',
                    'terms'    => ['main-gallery'],
                    'field'    => 'slug'
                ]
            ]
        ]);

        if (!$gallery_query->have_posts()) {
            return [];
        }

        $gallery_images = array_map('wp_prepare_attachment_for_js', $gallery_query->posts);

        return ['gallery' => $gallery_images];
    }
}
