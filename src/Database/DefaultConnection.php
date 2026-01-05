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

use PHPUnit\DbUnit\Database\Metadata\AbstractMetadata;
use PHPUnit\DbUnit\Database\Metadata\Metadata;
use PHPUnit\DbUnit\DataSet\IDataSet;
use PHPUnit\DbUnit\DataSet\ITable;
use PHPUnit\DbUnit\DataSet\QueryTable;

/**
 * Provides a basic interface for communicating with a database.
 */
class DefaultConnection implements Connection
{
    /**
     * The metadata object used to retrieve table meta data from the database.
     */
    protected AbstractMetadata $metaData;

    /**
     * Creates a new database connection
     */
    public function __construct(
        protected \PDO $connection,
        string $schema = ''
    ) {
        $this->metaData = AbstractMetadata::createMetaData($this->connection, $schema);
        $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Close this connection.
     */
    public function close(): void
    {
        unset($this->connection, $this->metaData);
    }

    /**
     * Returns a database metadata object that can be used to retrieve table
     * meta data from the database.
     */
    public function getMetaData(): Metadata
    {
        return $this->metaData;
    }

    /**
     * Returns the schema for the connection.
     */
    public function getSchema(): string
    {
        return $this->getMetaData()->getSchema();
    }

    /**
     * Creates a dataset containing the specified table names. If no table
     * names are specified then it will created a dataset over the entire
     * database.
     *
     *
     *
     * @todo Implement the filtered data set.
     */
    public function createDataSet(?array $tableNames = null): IDataSet
    {
        if ($tableNames === null || $tableNames === []) {
            return new DataSet($this);
        }

        return new FilteredDataSet($this, $tableNames);
    }

    /**
     * Creates a table with the result of the specified SQL statement.
     *
     * @param string $resultName
     * @param string $sql
     *
     * @return QueryTable
     */
    public function createQueryTable($resultName, $sql): ITable
    {
        return new QueryTable($resultName, $sql, $this);
    }

    /**
     * Returns this connection database configuration
     */
    public function getConfig(): void
    {
    }

    /**
     * Returns a PDO Connection
     */
    public function getConnection(): \PDO
    {
        return $this->connection;
    }

    /**
     * Returns the number of rows in the given table. You can specify an
     * optional where clause to return a subset of the table.
     *
     * @param string $tableName
     * @param string $whereClause
     */
    public function getRowCount($tableName, $whereClause = null): int
    {
        $query = 'SELECT COUNT(*) FROM ' . $this->quoteSchemaObject($tableName);

        if (isset($whereClause)) {
            $query .= ' WHERE ' . $whereClause;
        }

        return (int) $this->connection->query($query)->fetchColumn();
    }

    /**
     * Returns a quoted schema object. (table name, column name, etc)
     *
     *
     */
    public function quoteSchemaObject(string $object): string
    {
        return $this->getMetaData()->quoteSchemaObject($object);
    }

    /**
     * Returns the command used to truncate a table.
     */
    public function getTruncateCommand(): string
    {
        return $this->getMetaData()->getTruncateCommand();
    }

    /**
     * Returns true if the connection allows cascading
     */
    public function allowsCascading(): bool
    {
        return $this->getMetaData()->allowsCascading();
    }

    /**
     * Disables primary keys if connection does not allow setting them otherwise
     */
    public function disablePrimaryKeys(string $tableName): void
    {
        $this->getMetaData()->disablePrimaryKeys($tableName);
    }

    /**
     * Reenables primary keys after they have been disabled
     */
    public function enablePrimaryKeys(string $tableName): void
    {
        $this->getMetaData()->enablePrimaryKeys($tableName);
    }
}
