<?php
/*
 * This file is part of the Context package.
 *
 * (c) Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$vendor = dirname(dirname(__FILE__)) . '/vendor/';

if (!realpath($vendor)) {
    die('Please install via Composer before running tests.');
}

require_once $vendor . 'antecedent/patchwork/Patchwork.php';
require_once $vendor . 'phpunit/phpunit/src/Framework/Assert/Functions.php';
require_once $vendor . 'autoload.php';
require_once __DIR__ . '/stubs/stubs.php';
unset($vendor);
