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
use PHPUnit\DbUnit\DataSet\CompositeDataSet;
use PHPUnit\DbUnit\DataSet\DefaultDataSet;
use PHPUnit\DbUnit\DataSet\DefaultTable;
use PHPUnit\DbUnit\DataSet\DefaultTableMetadata;
use PHPUnit\DbUnit\DataSet\FlatXmlDataSet;
use PHPUnit\DbUnit\Operation\Truncate;
use PHPUnit\DbUnit\TestCase;

class OperationsMySQLTest extends TestCase
{
    protected function setUp(): void
    {
        if (!\extension_loaded('pdo_mysql')) {
            $this->markTestSkipped('pdo_mysql is required to run this test.');
        }

        if (getenv('MYSQL_DB_HOST') === false) {
            $this->markTestSkipped('No MySQL server configured for this test.');
        }

        parent::setUp();
    }

    public function testTruncate(): void
    {
        $truncateOperation = new Truncate();
        $truncateOperation->execute($this->getConnection(), $this->getDataSet());

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
                    ['table2_id', 'table1_id', 'column5', 'column6', 'column7', 'column8']
                )
            ),
            new DefaultTable(
                new DefaultTableMetadata(
                    'table3',
                    ['table3_id', 'table2_id', 'column9', 'column10', 'column11', 'column12']
                )
            ),
        ]);

        self::assertDataSetsEqual($expectedDataSet, $this->getConnection()->createDataSet());
    }

    public function getCompositeDataSet(): CompositeDataSet
    {
        $compositeDataset = new CompositeDataSet();

        $dataset = $this->createXMLDataSet(TEST_FILES_PATH . 'XmlDataSets/TruncateCompositeTest.xml');
        $compositeDataset->addDataSet($dataset);

        return $compositeDataset;
    }

    public function testTruncateComposite(): void
    {
        $truncateOperation = new Truncate();
        $truncateOperation->execute($this->getConnection(), $this->getCompositeDataSet());

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
                    ['table2_id', 'table1_id', 'column5', 'column6', 'column7', 'column8']
                )
            ),
            new DefaultTable(
                new DefaultTableMetadata(
                    'table3',
                    ['table3_id', 'table2_id', 'column9', 'column10', 'column11', 'column12']
                )
            ),
        ]);

        self::assertDataSetsEqual($expectedDataSet, $this->getConnection()->createDataSet());
    }

    protected function getConnection(): DefaultConnection
    {
        return new DefaultConnection(DatabaseTestUtility::getMySQLDB(), 'mysql');
    }

    protected function getDataSet(): FlatXmlDataSet
    {
        return new FlatXmlDataSet(TEST_FILES_PATH . 'XmlDataSets/OperationsMySQLTestFixture.xml');
    }
}
