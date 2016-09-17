<?php # -*- coding: utf-8 -*-
/*
 * This file is part of the Context package.
 *
 * (c) Giuseppe Mazzapica
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brain\Context\QueryPredicate;

/**
 * This class is an helper to obtain a callable (in form of invokable object) that receives a
 * conditional tag as constructor argument, and return true or false based if the query object
 * passed to the invoke method satisfy that conditional tag.
 *
 * Is optionally possible to pass an argument that will passed to the conditional tag.
 * For example:
 *
 * <code>
 * $predicate = new Predicate('is_singular', 'product');
 * $result = $predicate($wp_query);
 * </code>
 *
 * `$result` will be true if the given query object is a singular product query.
 *
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context
 * @license http://opensource.org/licenses/MIT MIT
 */
final class Predicate
{
    /**
     * @var string
     */
    private $condition;

    /**
     * @var mixed
     */
    private $param;

    /**
     * @param string $condition
     * @param mixed $param
     */
    public function __construct($condition, $param = null)
    {
        $this->condition = is_string($condition) ? $condition : '';
        $this->param = $param;
    }

    /**
     * @param \WP_Query $query
     * @return bool
     */
    public function __invoke(\WP_Query $query)
    {
        if (!$this->condition) {
            return false;
        }

        $method = [$query, $this->condition];
        if (!is_callable($method)) {
            return false;
        }

        if (is_null($this->param)) {
            return $method();
        }

        return $method($this->param);
    }
}
