<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the Context package.
 *
 * (c) Giuseppe Mazzapica
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (!class_exists('WP_Comment_Query')) {
    class WP_Comment_Query
    {

        public $args = [];

        public function __construct()
        {
            $this->args = func_get_args();
        }
    }
}

if (!class_exists('WP_Query')) {
    class WP_Query
    {

        public $args = [];

        public function __construct()
        {
            $this->args = func_get_args();
        }
    }
}
