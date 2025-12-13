<?php

/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit\Database;

use PHPUnit\DbUnit\Database\Metadata\Metadata;
use PHPUnit\DbUnit\DataSet\IDataSet;
use PHPUnit\DbUnit\DataSet\ITable;

/**
 * Provides a basic interface for communicating with a database.
 */
interface Connection
{
    /**
     * Close this connection.
     */
    public function close();

    /**
     * Creates a dataset containing the specified table names. If no table
     * names are specified then it will created a dataset over the entire
     * database.
     *
     * @param array|null $tableNames
     *
     * @return IDataSet
     */
    public function createDataSet(?array $tableNames = null): IDataSet;

    /**
     * Creates a table with the result of the specified SQL statement.
     *
     * @param string $resultName
     * @param string $sql
     *
     * @return ITable
     */
    public function createQueryTable($resultName, $sql): ITable;

    /**
     * Returns a PDO Connection
     *
     * @return \PDO
     */
    public function getConnection(): \PDO;

    /**
     * Returns a database metadata object that can be used to retrieve table
     * meta data from the database.
     *
     * @return Metadata
     */
    public function getMetaData(): Metadata;

    /**
     * Returns the number of rows in the given table. You can specify an
     * optional where clause to return a subset of the table.
     *
     * @param string $tableName
     * @param string $whereClause
     *
     * @return int
     */
    public function getRowCount(string $tableName, $whereClause = null): int;

    /**
     * Returns the schema for the connection.
     *
     * @return string
     */
    public function getSchema(): string;

    /**
     * Returns a quoted schema object. (table name, column name, etc)
     *
     * @param string $object
     *
     * @return string
     */
    public function quoteSchemaObject(string $object): string;

    /**
     * Returns the command used to truncate a table.
     *
     * @return string
     */
    public function getTruncateCommand(): string;

    /**
     * Returns true if the connection allows cascading
     *
     * @return bool
     */
    public function allowsCascading(): bool;

    /**
     * Disables primary keys if connection does not allow setting them otherwise
     *
     * @param string $tableName
     */
    public function disablePrimaryKeys(string $tableName): void;

    /**
     * Reenables primary keys after they have been disabled
     *
     * @param string $tableName
     */
    public function enablePrimaryKeys(string $tableName): void;
}
