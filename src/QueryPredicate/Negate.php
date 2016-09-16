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
 * callable and when called passing a query object returns the negation of the received callable.
 *
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @package Context
 * @license http://opensource.org/licenses/MIT MIT
 */
final class Negate
{

    /**
     * @var callable
     */
    private $predicate;

    /**
     * @param callable $predicate
     */
    public function __construct(callable $predicate)
    {
        $this->predicate = $predicate;
    }

    /**
     * @param \WP_Query $query
     * @return bool
     */
    public function __invoke(\WP_Query $query)
    {
        $predicate = $this->predicate;

        return !$predicate($query);
    }
}
