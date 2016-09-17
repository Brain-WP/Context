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
 * This class is an helper to obtain a callable (in form of invokable object) that return true
 * or false based on a collection of other predicates.
 *
 * It can work either in `AND` or in `OR` mode.
 * In `AND` mode, the predicate returns true if all predicates are satisfied.
 * In `OR` mode, the predicate returns true if any of predicates is satisfied.
 *
 * Default mode is `AND`. It can be changed to `OR` passing `QueryPredicates::MODE_OR` flag to
 * constructor as second argument.
 *
 * Example:
 *
 * <code>
 * $predicate = new Merge([
 *      new Predicates(['is_single', 'is_search']),
 *      new Negate(new Predicate('is_singular', 'product'))
 * ]);
 *
 * $result = $predicate($wp_query);
 * </code>
 *
 * In the example above, `$result` is for a single post of any post type except 'product' and for
 * search queries.
 *
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context
 * @license http://opensource.org/licenses/MIT MIT
 */
final class Merge
{

    /**
     * @var callable[]
     */
    private $predicates;

    /**
     * @var int
     */
    private $flags = 0;


    /**
     * @param array $predicates
     * @param int $flags
     */
    public function __construct(array $predicates, $flags = Predicates::MODE_AND)
    {
        $this->predicates = array_filter($predicates, function (callable $predicate) {
            return $predicate;
        });

        is_int($flags) and $this->flags = $flags;
    }

    /**
     * @param \WP_Query $query
     * @return bool
     */
    public function __invoke(\WP_Query $query)
    {
        if (!$this->predicates) {
            return false;
        }

        $is_and = ($this->flags & Predicates::MODE_AND) > 0;

        /** @var callable $predicate */
        foreach ($this->predicates as $predicate) {
            $accept = $predicate($query);

            // when in OR mode, we return true at first success
            if ($accept && !$is_and) {
                return true;
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
