<?php

/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit\Tests\Operation;

use DatabaseTestUtility;
use PHPUnit\DbUnit\Database\DefaultConnection;
use PHPUnit\DbUnit\DataSet\DefaultDataSet;
use PHPUnit\DbUnit\DataSet\DefaultTable;
use PHPUnit\DbUnit\DataSet\DefaultTableMetadata;
use PHPUnit\DbUnit\DataSet\FlatXmlDataSet;
use PHPUnit\DbUnit\Operation\Delete;
use PHPUnit\DbUnit\Operation\DeleteAll;
use PHPUnit\DbUnit\Operation\Insert;
use PHPUnit\DbUnit\Operation\Replace;
use PHPUnit\DbUnit\Operation\Truncate;
use PHPUnit\DbUnit\Operation\Update;
use PHPUnit\DbUnit\TestCase;

class OperationsTest extends TestCase
{
    protected function setUp(): void
    {
        if (!\extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('PDO/SQLite is required to run this test.');
        }

        parent::setUp();
    }

    public function testDelete(): void
    {
        $deleteOperation = new Delete();

        $deleteOperation->execute(
            $this->getConnection(),
            new FlatXmlDataSet(TEST_FILES_PATH . 'XmlDataSets/DeleteOperationTest.xml')
        );

        self::assertDataSetsEqual(
            new FlatXmlDataSet(TEST_FILES_PATH . 'XmlDataSets/DeleteOperationResult.xml'),
            $this->getConnection()->createDataSet()
        );
    }

    public function testDeleteAll(): void
    {
        $deleteAllOperation = new DeleteAll();

        $deleteAllOperation->execute(
            $this->getConnection(),
            new FlatXmlDataSet(TEST_FILES_PATH . 'XmlDataSets/DeleteAllOperationTest.xml')
        );

        $expectedDataSet = new DefaultDataSet([
            new DefaultTable(
                new DefaultTableMetadata(
                    'table1',
                    ['table1_id', 'column1', 'column2', 'column3', 'column4']
                )
            ),
            new DefaultTable(
                new DefaultTableMetadata(
                    'table2',
                    ['table2_id', 'column5', 'column6', 'column7', 'column8']
                )
            ),
            new DefaultTable(
                new DefaultTableMetadata(
                    'table3',
                    ['table3_id', 'column9', 'column10', 'column11', 'column12']
                )
            ),
        ]);

        self::assertDataSetsEqual($expectedDataSet, $this->getConnection()->createDataSet());
    }

    public function testTruncate(): void
    {
        $truncateOperation = new Truncate();

        $truncateOperation->execute(
            $this->getConnection(),
            new FlatXmlDataSet(TEST_FILES_PATH . 'XmlDataSets/DeleteAllOperationTest.xml')
        );

        $expectedDataSet = new DefaultDataSet([
            new DefaultTable(
                new DefaultTableMetadata(
                    'table1',
                    ['table1_id', 'column1', 'column2', 'column3', 'column4']
                )
            ),
            new DefaultTable(
                new DefaultTableMetadata(
                    'table2',
                    ['table2_id', 'column5', 'column6', 'column7', 'column8']
                )
            ),
            new DefaultTable(
                new DefaultTableMetadata(
                    'table3',
                    ['table3_id', 'column9', 'column10', 'column11', 'column12']
                )
            ),
        ]);

        self::assertDataSetsEqual($expectedDataSet, $this->getConnection()->createDataSet());
    }

    public function testInsert(): void
    {
        $insertOperation = new Insert();

        $insertOperation->execute(
            $this->getConnection(),
            new FlatXmlDataSet(TEST_FILES_PATH . 'XmlDataSets/InsertOperationTest.xml')
        );

        self::assertDataSetsEqual(
            new FlatXmlDataSet(TEST_FILES_PATH . 'XmlDataSets/InsertOperationResult.xml'),
            $this->getConnection()->createDataSet()
        );
    }

    public function testUpdate(): void
    {
        $updateOperation = new Update();

        $updateOperation->execute(
            $this->getConnection(),
            new FlatXmlDataSet(TEST_FILES_PATH . 'XmlDataSets/UpdateOperationTest.xml')
        );

        self::assertDataSetsEqual(
            new FlatXmlDataSet(TEST_FILES_PATH . 'XmlDataSets/UpdateOperationResult.xml'),
            $this->getConnection()->createDataSet()
        );
    }

    public function testReplace(): void
    {
        $replaceOperation = new Replace();

        $replaceOperation->execute(
            $this->getConnection(),
            new FlatXmlDataSet(TEST_FILES_PATH . 'XmlDataSets/ReplaceOperationTest.xml')
        );

        self::assertDataSetsEqual(
            new FlatXmlDataSet(TEST_FILES_PATH . 'XmlDataSets/ReplaceOperationResult.xml'),
            $this->getConnection()->createDataSet()
        );
    }

    public function testInsertEmptyTable(): void
    {
        $insertOperation = new Insert();

        $insertOperation->execute(
            $this->getConnection(),
            new FlatXmlDataSet(TEST_FILES_PATH . 'XmlDataSets/EmptyTableInsertTest.xml')
        );

        self::assertDataSetsEqual(
            new FlatXmlDataSet(TEST_FILES_PATH . 'XmlDataSets/EmptyTableInsertResult.xml'),
            $this->getConnection()->createDataSet()
        );
    }

    public function testInsertAllEmptyTables(): void
    {
        $insertOperation = new Insert();

        $insertOperation->execute(
            $this->getConnection(),
            new FlatXmlDataSet(TEST_FILES_PATH . 'XmlDataSets/AllEmptyTableInsertTest.xml')
        );

        self::assertDataSetsEqual(
            new FlatXmlDataSet(TEST_FILES_PATH . 'XmlDataSets/AllEmptyTableInsertResult.xml'),
            $this->getConnection()->createDataSet()
        );
    }

    protected function getConnection(): DefaultConnection
    {
        return new DefaultConnection(DatabaseTestUtility::getSQLiteMemoryDB(), 'sqlite');
    }

    protected function getDataSet(): FlatXmlDataSet
    {
        return new FlatXmlDataSet(TEST_FILES_PATH . 'XmlDataSets/OperationsTestFixture.xml');
    }
}
