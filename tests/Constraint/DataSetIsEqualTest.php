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

namespace PHPUnit\DbUnit\Tests\Constraint;

use PHPUnit\DbUnit\Constraint\DataSetIsEqual;
use PHPUnit\DbUnit\DataSet\DefaultDataSet;
use PHPUnit\DbUnit\DataSet\DefaultTable;
use PHPUnit\DbUnit\DataSet\DefaultTableMetadata;
use PHPUnit\DbUnit\Exception\InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

class DataSetIsEqualTest extends TestCase
{
    public function testMatch(): void
    {
        $constraint = $this->createConstraint();

        $dataSet = new DefaultDataSet([
            new DefaultTable(
                new DefaultTableMetadata('table1', ['id', 'col1'], ['id']),
            ),
            new DefaultTable(
                new DefaultTableMetadata('table2', ['id', 'col2'], ['id']),
            ),
        ]);

        self::assertTrue($constraint->evaluate($dataSet, '', true));
        self::assertFalse($constraint->evaluate(new DefaultDataSet(), '', true));
        self::assertSame('is equal to expected dataset [table1,table2]', $constraint->toString());
    }

    public function testMatchInvalidType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Only dataset instance can be matched');

        $constraint = $this->createConstraint();
        $constraint->evaluate(null);
    }

    public function testExpectationFailed(): void
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('Failed asserting that [] is equal to expected dataset [table1,table2].');

        self::assertThat(new DefaultDataSet(), $this->createConstraint());
    }

    private function createConstraint(): DataSetIsEqual
    {
        $dataSet = new DefaultDataSet([
            new DefaultTable(
                new DefaultTableMetadata('table1', ['id', 'col1'], ['id']),
            ),
            new DefaultTable(
                new DefaultTableMetadata('table2', ['id', 'col2'], ['id']),
            ),
        ]);

        return new DataSetIsEqual($dataSet);
    }
}
