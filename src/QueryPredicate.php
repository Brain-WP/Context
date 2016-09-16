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
final class QueryPredicate
{

    /**
     * @var string[]
     */
    private $conditions;

    /**
     * @param string|string[] $conditional
     */
    public function __construct($conditional)
    {
        is_string($conditional) and $conditional = [$conditional];

        $this->conditions = is_array($conditional) ? array_filter($conditional, 'is_string') : [];
    }

    /**
     * @param \WP_Query $query
     * @return bool
     */
    public function __invoke(\WP_Query $query)
    {
        if (!$this->conditions) {
            return false;
        }

        foreach ($this->conditions as $conditional) {
            $method = [$query, $conditional];
            if (!is_callable($method)) {
                return false;
            }
            if (!$method()) {
                return false;
            }
        }

        return true;
    }
}
