<?php

/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit\DataSet;

use PHPUnit\DbUnit\Database\Connection;

/**
 * Provides the functionality to represent a database table.
 */
class QueryTable extends AbstractTable
{
    /**
     * @var string
     */
    protected $query;

    /**
     * @var Connection
     */
    protected $databaseConnection;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * Creates a new database query table object.
     */
    public function __construct(string $tableName, string $query, Connection $databaseConnection)
    {
        $this->tableName = $tableName;
        $this->query = $query;
        $this->databaseConnection = $databaseConnection;
    }

    /**
     * Returns the table's meta data.
     */
    public function getTableMetaData(): ITableMetadata
    {
        $this->createTableMetaData();

        return parent::getTableMetaData();
    }

    /**
     * Checks if a given row is in the table
     *
     *
     */
    public function assertContainsRow(array $row): bool
    {
        $this->loadData();

        return parent::assertContainsRow($row);
    }

    /**
     * Returns the number of rows in this table.
     */
    public function getRowCount(): int
    {
        $this->loadData();

        return parent::getRowCount();
    }

    /**
     * Returns the value for the given column on the given row.
     */
    public function getValue(int $row, string $column): mixed
    {
        $this->loadData();

        return parent::getValue($row, $column);
    }

    /**
     * Returns the an associative array keyed by columns for the given row.
     *
     *
     */
    public function getRow(int $row): array
    {
        $this->loadData();

        return parent::getRow($row);
    }

    /**
     * Asserts that the given table matches this table.
     *
     *
     */
    public function matches(ITable $other): bool
    {
        $this->loadData();

        return parent::matches($other);
    }

    protected function loadData(): void
    {
        if ($this->data === null) {
            $pdoStatement = $this->databaseConnection->getConnection()->query($this->query);
            $this->data = $pdoStatement->fetchAll(\PDO::FETCH_ASSOC);
        }
    }

    protected function createTableMetaData(): void
    {
        if ($this->tableMetaData === null) {
            $this->loadData();

            // if some rows are in the table
            $columns = [];

            if (isset($this->data[0])) {
                // get column names from data
                $columns = array_keys($this->data[0]);
            } else {
                $columns = $this->databaseConnection->getMetaData()->getTableColumns($this->tableName);
            }

            // create metadata
            $this->tableMetaData = new DefaultTableMetadata($this->tableName, $columns);
        }
    }
}
