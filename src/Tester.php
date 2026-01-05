<?php

/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit;

use PHPUnit\DbUnit\Database\Connection;
use PHPUnit\DbUnit\DataSet\IDataSet;
use PHPUnit\DbUnit\Operation\Operation;

/**
 * This is the interface for DatabaseTester objects. These objects are used to
 * add database testing to existing test cases using composition instead of
 * extension.
 */
interface Tester
{
    /**
     * Closes the specified connection.
     */
    public function closeConnection(Connection $connection): void;

    /**
     * Returns the test database connection.
     */
    public function getConnection(): Connection;

    /**
     * Returns the test dataset.
     */
    public function getDataSet(): IDataSet;

    /**
     * TestCases must call this method inside setUp().
     */
    public function onSetUp(): void;

    /**
     * TestCases must call this method inside tearDown().
     */
    public function onTearDown(): void;

    /**
     * Sets the test dataset to use.
     */
    public function setDataSet(IDataSet $dataSet): void;

    /**
     * Sets the schema value.
     */
    public function setSchema(string $schema): void;

    /**
     * Sets the DatabaseOperation to call when starting the test.
     */
    public function setSetUpOperation(Operation $setUpOperation): void;

    /**
     * Sets the DatabaseOperation to call when stopping the test.
     */
    public function setTearDownOperation(Operation $tearDownOperation): void;
}
