<?php

declare(strict_types=1);

/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\DbUnit\Constraint;

use PHPUnit\DbUnit\DataSet\IDataSet;
use PHPUnit\DbUnit\Exception\InvalidArgumentException;
use PHPUnit\Framework\Constraint\Constraint;

/**
 * Asserts whether or not two dbunit datasets are equal.
 */
class DataSetIsEqual extends Constraint
{
    public function __construct(
        protected IDataSet $value
    ) {
    }

    /**
     * Returns a string representation of the constraint.
     */
    public function toString(): string
    {
        return sprintf(
            'is equal to expected dataset %s',
            $this->value
        );
    }

    /**
     * Evaluates the constraint for parameter $other. Returns TRUE if the
     * constraint is met, FALSE otherwise.
     *
     * This method can be overridden to implement the evaluation algorithm.
     *
     * @param IDataSet $other value or object to evaluate
     */
    protected function matches(mixed $other): bool
    {
        if (!$other instanceof IDataSet) {
            throw new InvalidArgumentException('Only dataset instance can be matched');
        }

        return $this->value->matches($other);
    }

    /**
     * Returns the description of the failure
     *
     * The beginning of failure messages is "Failed asserting that" in most
     * cases. This method should return the second part of that sentence.
     *
     * @param mixed $other evaluated value or object
     */
    protected function failureDescription(mixed $other): string
    {
        return $other . ' ' . $this->toString();
    }
}
