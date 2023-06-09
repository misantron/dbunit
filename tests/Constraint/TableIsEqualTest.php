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

use PHPUnit\DbUnit\Constraint\TableIsEqual;
use PHPUnit\DbUnit\DataSet\DefaultTable;
use PHPUnit\DbUnit\DataSet\DefaultTableMetadata;
use PHPUnit\DbUnit\Exception\InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

class TableIsEqualTest extends TestCase
{
    public function testMatch(): void
    {
        $constraint = $this->createConstraint();

        $table1 = new DefaultTable(
            new DefaultTableMetadata('table1', ['id', 'col1'], ['id']),
        );
        $table2 = new DefaultTable(
            new DefaultTableMetadata('table2', ['id', 'col2'], ['id']),
        );

        self::assertTrue($constraint->evaluate($table1, '', true));
        self::assertFalse($constraint->evaluate($table2, '', true));
        self::assertSame(
            sprintf('is equal to expected %s', $table1),
            $constraint->toString()
        );
    }

    public function testMatchInvalidType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Only table instance can be matched');

        $constraint = $this->createConstraint();
        $constraint->evaluate(null);
    }

    public function testExpectationFailed(): void
    {
        $table1 = new DefaultTable(
            new DefaultTableMetadata('table1', ['id', 'col1'], ['id']),
        );
        $table2 = new DefaultTable(
            new DefaultTableMetadata('table2', ['id', 'col2'], ['id']),
        );

        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Failed asserting that %s is equal to expected %s.',
                $table2,
                $table1
            )
        );

        self::assertThat($table2, $this->createConstraint());
    }

    private function createConstraint(): TableIsEqual
    {
        $table = new DefaultTable(
            new DefaultTableMetadata('table1', ['id', 'col1'], ['id']),
        );

        return new TableIsEqual($table);
    }
}
