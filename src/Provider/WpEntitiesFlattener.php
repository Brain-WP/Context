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
class WpEntitiesFlattener extends BaseEntitiesFlattener
{
    /**
     * @param mixed $item
     * @return mixed
     */
    protected function processItem($item)
    {
        switch (true) {
            case is_array($item):
                return array_map([$this, 'processItem'], $item);
            case $this->isWpEntity($item):
                return (object)$item->to_array();
            case ($item instanceof \WP_Query):
            case ($item instanceof \WP_Date_Query):
            case ($item instanceof \WP_Meta_Query):
            case ($item instanceof \WP):
            case ($item instanceof \stdClass):
                return (object)array_map([$this, 'processItem'], get_object_vars($item));
            default:
                return $item;
        }
    }
}
