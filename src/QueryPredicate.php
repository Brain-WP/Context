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
 * This class is an helper to obtain a callable (in form of invokable object) that receives a
 * query object as single argument, and return true or false based on the query object satisfy
 * one or conditional tag.
 *
 * When many conditional tags are given, it can work either in `AND` or in `OR` mode.
 * In `AND` mode, the predicate returns true if all conditions are satisfied.
 * In `OR` mode, the predicate returns true if any of conditions is satisfied.
 *
 * For example:
 *
 * <code>
 * $predicate = new QueryPredicate(['is_front_page', 'is_page'], QueryPredicate::MODE_AND);
 * $result = $predicate($wp_query);
 * </code>
 *
 * In the example above, `$result` is true if the query object is front page *and* a page.
 * On the contrary, if the code is:
 *
 * <code>
 * $predicate = new QueryPredicate(['is_front_page', 'is_page'], QueryPredicate::MODE_OR);
 * $result = $predicate($wp_query);
 * </code>
 *
 * `$result` is true if the query object is a front page *or* a page.
 *
 * Note that a single predicate as string is perfectly valid argument:
 *
 * <code>
 * $predicate = new QueryPredicate('is_front_page');
 * $result = $predicate($wp_query);
 * </code>
 *
 * In this case there's no need to pass a mode flag because it is irrelevant with one single tag.
 *
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context
 * @license http://opensource.org/licenses/MIT MIT
 */
final class QueryPredicate
{

    const MODE_AND = 1;
    const MODE_OR = 2;

    /**
     * @var string[]
     */
    private $conditions;

    /**
     * @var int
     */
    private $flags = 0;

    /**
     * @param string|string[] $conditional
     * @param int $flags
     */
    public function __construct($conditional, $flags = self::MODE_OR)
    {
        is_string($conditional) and $conditional = [$conditional];

        $this->conditions = is_array($conditional) ? array_filter($conditional, 'is_string') : [];
        is_int($flags) and $this->flags = $flags;
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

        $is_and = ($this->flags & self::MODE_AND) > 0;

        foreach ($this->conditions as $conditional) {
            /** @var callable $method */
            $method = [$query, $conditional];
            $is_callable = is_callable($method);
            // when in AND mode, we return false at first failure
            if (!$is_callable && $is_and) {
                return false;
            }

            $accept = $is_callable ? $method() : false;

            // when in OR mode, we return true at first success
            if ($accept && ! $is_and) {
                return TRUE;
            }

            // when in AND mode, we return false at first failure
            if (!$accept && $is_and) {
                return false;
            }
        }

        // If here, either:
        // - we were in OR mode and none of the conditions was satisfied;
        // - we were in AND mode and all the conditions were satisfied.
        // So we return true in latter case, false in the former.

        return $is_and;
    }
}
