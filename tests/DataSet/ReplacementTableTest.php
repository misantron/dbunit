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

namespace PHPUnit\DbUnit\Tests\DataSet;

use PHPUnit\DbUnit\DataSet\DefaultTable;
use PHPUnit\DbUnit\DataSet\DefaultTableMetadata;
use PHPUnit\DbUnit\DataSet\ITable;
use PHPUnit\DbUnit\DataSet\ITableMetadata;
use PHPUnit\DbUnit\DataSet\ReplacementTable;
use PHPUnit\DbUnit\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;

final class ReplacementTableTest extends \PHPUnit\Framework\TestCase
{
    private DefaultTable $startingTable;

    protected function setUp(): void
    {
        $tableMetaData = new DefaultTableMetadata(
            'table1',
            ['table1_id', 'column1', 'column2', 'column3', 'column4']
        );

        $table = new DefaultTable($tableMetaData);

        $table->addRow([
            'table1_id' => 1,
            'column1' => 'My name is %%%name%%%',
            'column2' => 200,
            'column3' => 34.64,
            'column4' => 'yghkf;a  hahfg8ja h;',
        ]);
        $table->addRow([
            'table1_id' => 2,
            'column1' => 'hk;afg',
            'column2' => 654,
            'column3' => 46.54,
            'column4' => '24rwehhads',
        ]);
        $table->addRow([
            'table1_id' => 3,
            'column1' => 'ha;gyt',
            'column2' => 462,
            'column3' => '[NULL] not really',
            'column4' => '[NULL]',
        ]);

        $this->startingTable = $table;
    }

    public function testNoReplacement(): void
    {
        TestCase::assertTablesEqual(
            $this->startingTable,
            new ReplacementTable($this->startingTable)
        );
    }

    public function testFullReplacement(): void
    {
        $tableMetaData = new DefaultTableMetadata(
            'table1',
            ['table1_id', 'column1', 'column2', 'column3', 'column4']
        );

        $table = new DefaultTable($tableMetaData);

        $table->addRow([
            'table1_id' => 1,
            'column1' => 'My name is %%%name%%%',
            'column2' => 200,
            'column3' => 34.64,
            'column4' => 'yghkf;a  hahfg8ja h;',
        ]);
        $table->addRow([
            'table1_id' => 2,
            'column1' => 'hk;afg',
            'column2' => 654,
            'column3' => 46.54,
            'column4' => '24rwehhads',
        ]);
        $table->addRow([
            'table1_id' => 3,
            'column1' => 'ha;gyt',
            'column2' => 462,
            'column3' => '[NULL] not really',
            'column4' => null,
        ]);

        $actual = new ReplacementTable($this->startingTable);
        $actual->addFullReplacement('[NULL]', null);

        TestCase::assertTablesEqual($table, $actual);
    }

    public function testSubStrReplacement(): void
    {
        $tableMetaData = new DefaultTableMetadata(
            'table1',
            ['table1_id', 'column1', 'column2', 'column3', 'column4']
        );

        $table = new DefaultTable($tableMetaData);

        $table->addRow([
            'table1_id' => 1,
            'column1' => 'My name is Mike Lively',
            'column2' => 200,
            'column3' => 34.64,
            'column4' => 'yghkf;a  hahfg8ja h;',
        ]);
        $table->addRow([
            'table1_id' => 2,
            'column1' => 'hk;afg',
            'column2' => 654,
            'column3' => 46.54,
            'column4' => '24rwehhads',
        ]);
        $table->addRow([
            'table1_id' => 3,
            'column1' => 'ha;gyt',
            'column2' => 462,
            'column3' => '[NULL] not really',
            'column4' => '[NULL]',
        ]);

        $actual = new ReplacementTable($this->startingTable);
        $actual->addSubStrReplacement('%%%name%%%', 'Mike Lively');

        TestCase::assertTablesEqual($table, $actual);
    }

    public function testConstructorReplacements(): void
    {
        $tableMetaData = new DefaultTableMetadata(
            'table1',
            ['table1_id', 'column1', 'column2', 'column3', 'column4']
        );

        $table = new DefaultTable($tableMetaData);

        $table->addRow([
            'table1_id' => 1,
            'column1' => 'My name is Mike Lively',
            'column2' => 200,
            'column3' => 34.64,
            'column4' => 'yghkf;a  hahfg8ja h;',
        ]);
        $table->addRow([
            'table1_id' => 2,
            'column1' => 'hk;afg',
            'column2' => 654,
            'column3' => 46.54,
            'column4' => '24rwehhads',
        ]);
        $table->addRow([
            'table1_id' => 3,
            'column1' => 'ha;gyt',
            'column2' => 462,
            'column3' => '[NULL] not really',
            'column4' => null,
        ]);

        $actual = new ReplacementTable(
            $this->startingTable,
            [
                '[NULL]' => null,
            ],
            [
                '%%%name%%%' => 'Mike Lively',
            ]
        );

        TestCase::assertTablesEqual($table, $actual);
    }

    public function testGetRow(): void
    {
        $actual = new ReplacementTable(
            $this->startingTable,
            [
                '[NULL]' => null,
            ],
            [
                '%%%name%%%' => 'Mike Lively',
            ]
        );

        $this->assertSame(
            [
                'table1_id' => '1',
                'column1' => 'My name is Mike Lively',
                'column2' => '200',
                'column3' => '34.64',
                'column4' => 'yghkf;a  hahfg8ja h;',
            ],
            $actual->getRow(0)
        );

        $this->assertEquals(
            [
                'table1_id' => 3,
                'column1' => 'ha;gyt',
                'column2' => 462,
                'column3' => '[NULL] not really',
                'column4' => null,
            ],
            $actual->getRow(2)
        );
    }

    public function testGetValue(): void
    {
        $actual = new ReplacementTable(
            $this->startingTable,
            [
                '[NULL]' => null,
            ],
            [
                '%%%name%%%' => 'Mike Lively',
            ]
        );

        $this->assertNull($actual->getValue(2, 'column4'));
        $this->assertEquals('My name is Mike Lively', $actual->getValue(0, 'column1'));
    }

    public function testMatchesWithNonMatchingMetaData(): void
    {
        $tableMetaData = $this->createMock(ITableMetadata::class);
        $otherMetaData = $this->createStub(ITableMetadata::class);
        $table = $this->createMock(ITable::class);
        $otherTable = $this->createMock(ITable::class);

        $table->expects($this->once())
            ->method('getTableMetaData')
            ->willReturn($tableMetaData);

        $otherTable->expects($this->once())
            ->method('getTableMetaData')
            ->willReturn($otherMetaData);

        $tableMetaData->expects($this->once())
            ->method('matches')
            ->with($otherMetaData)
            ->willReturn(false);

        $replacementTable = new ReplacementTable($table);
        $this->assertFalse($replacementTable->matches($otherTable));
    }

    public function testMatchesWithNonMatchingRowCount(): void
    {
        $tableMetaData = $this->createMock(ITableMetadata::class);
        $otherMetaData = $this->createStub(ITableMetadata::class);
        $table = $this->createMock(ITable::class);
        $otherTable = $this->createMock(ITable::class);

        /** @var MockObject|ReplacementTable $replacementTable */
        $replacementTable = $this->getMockBuilder(ReplacementTable::class)
            ->setConstructorArgs([$table])
            ->onlyMethods(['getRowCount'])
            ->getMock();

        $table->expects($this->once())
            ->method('getTableMetaData')
            ->willReturn($tableMetaData);

        $otherTable->expects($this->once())
            ->method('getTableMetaData')
            ->willReturn($otherMetaData);
        $otherTable->expects($this->once())
            ->method('getRowCount')
            ->willReturn(0);

        $tableMetaData->expects($this->once())
            ->method('matches')
            ->with($otherMetaData)
            ->willReturn(true);

        $replacementTable->expects($this->once())
            ->method('getRowCount')
            ->willReturn(1);

        $this->assertFalse($replacementTable->matches($otherTable));
    }

    #[DataProvider('providerMatchesWithColumnValueComparisons')]
    public function testMatchesWithColumnValueComparisons(array $tableColumnValues, array $otherColumnValues, bool $matches): void
    {
        $tableMetaData = $this->createMock(ITableMetadata::class);
        $otherMetaData = $this->createStub(ITableMetadata::class);
        $table = $this->createMock(ITable::class);
        $otherTable = $this->createMock(ITable::class);

        $table->expects($this->once())
            ->method('getTableMetaData')
            ->willReturn($tableMetaData);

        $otherTable->expects($this->once())
            ->method('getTableMetaData')
            ->willReturn($otherMetaData);
        $otherTable->expects($this->once())
            ->method('getRowCount')
            ->willReturn(\count($otherColumnValues));

        $tableMetaData->expects($this->once())
            ->method('getColumns')
            ->willReturn(array_keys(reset($tableColumnValues)));
        $tableMetaData->expects($this->once())
            ->method('matches')
            ->with($otherMetaData)
            ->willReturn(true);

        /** @var MockObject|ReplacementTable $replacementTable */
        $replacementTable = $this->getMockBuilder(ReplacementTable::class)
            ->setConstructorArgs([$table])
            ->onlyMethods(['getRowCount', 'getValue'])
            ->getMock();

        $replacementTable
            ->method('getRowCount')
            ->willReturn(\count($tableColumnValues));

        $tableMap = [];
        $otherMap = [];

        foreach ($tableColumnValues as $rowIndex => $rowData) {
            foreach ($rowData as $columnName => $columnValue) {
                $tableMap[] = [$rowIndex, $columnName, $columnValue];
                $otherMap[] = [$rowIndex, $columnName, $otherColumnValues[$rowIndex][$columnName]];
            }
        }

        $replacementTable
            ->method('getValue')
            ->willReturnMap($tableMap);
        $otherTable
            ->method('getValue')
            ->willReturnMap($otherMap);

        $this->assertSame($matches, $replacementTable->matches($otherTable));
    }

    public static function providerMatchesWithColumnValueComparisons(): \Iterator
    {
        // One row, one column, matches
        yield [
            [
                [
                    'id' => 1,
                ],
            ],
            [
                [
                    'id' => 1,
                ],
            ],
            true,
        ];
        // One row, one column, does not match
        yield [
            [
                [
                    'id' => 1,
                ],
            ],
            [
                [
                    'id' => 2,
                ],
            ],
            false,
        ];
        // Multiple rows, one column, matches
        yield [
            [
                [
                    'id' => 1,
                ],
                [
                    'id' => 2,
                ],
            ],
            [
                [
                    'id' => 1,
                ],
                [
                    'id' => 2,
                ],
            ],
            true,
        ];
        // Multiple rows, one column, do not match
        yield [
            [
                [
                    'id' => 1,
                ],
                [
                    'id' => 2,
                ],
            ],
            [
                [
                    'id' => 1,
                ],
                [
                    'id' => 3,
                ],
            ],
            false,
        ];
        // Multiple rows, multiple columns, matches
        yield [
            [
                [
                    'id' => 1,
                    'name' => 'foo',
                ],
                [
                    'id' => 2,
                    'name' => 'bar',
                ],
            ],
            [
                [
                    'id' => 1,
                    'name' => 'foo',
                ],
                [
                    'id' => 2,
                    'name' => 'bar',
                ],
            ],
            true,
        ];
        // Multiple rows, multiple columns, do not match
        yield [
            [
                [
                    'id' => 1,
                    'name' => 'foo',
                ],
                [
                    'id' => 2,
                    'name' => 'bar',
                ],
            ],
            [
                [
                    'id' => 1,
                    'name' => 'foo',
                ],
                [
                    'id' => 2,
                    'name' => 'baz',
                ],
            ],
            false,
        ];
        // Int and int as string must match
        yield [
            [
                [
                    'id' => 42,
                ],
            ],
            [
                [
                    'id' => '42',
                ],
            ],
            true,
        ];
        // Float and float as string must match
        yield [
            [
                [
                    'id' => 15.3,
                ],
            ],
            [
                [
                    'id' => '15.3',
                ],
            ],
            true,
        ];
        // Int and float must match
        yield [
            [
                [
                    'id' => 18.00,
                ],
            ],
            [
                [
                    'id' => 18,
                ],
            ],
            true,
        ];
        // 0 and empty string must not match
        yield [
            [
                [
                    'id' => 0,
                ],
            ],
            [
                [
                    'id' => '',
                ],
            ],
            false,
        ];
        // 0 and null must not match
        yield [
            [
                [
                    'id' => 0,
                ],
            ],
            [
                [
                    'id' => null,
                ],
            ],
            false,
        ];
        // empty string and null must not match
        yield [
            [
                [
                    'id' => '',
                ],
            ],
            [
                [
                    'id' => null,
                ],
            ],
            false,
        ];
    }
}
