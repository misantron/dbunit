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

use PHPUnit\DbUnit\DataSet\ITable;
use PHPUnit\DbUnit\DataSet\ITableIterator;
use PHPUnit\DbUnit\DataSet\ITableMetadata;

/**
 * Provides iterative access to tables from a database instance.
 */
class TableIterator implements ITableIterator
{
    public function __construct(
        private array $tableNames,
        private readonly DataSet $dataSet,
        private readonly bool $reverse = false,
    ) {
        $this->rewind();
    }

    /**
     * Returns the current table.
     */
    public function getTable(): ITable
    {
        return $this->current();
    }

    /**
     * Returns the current table's meta data.
     */
    public function getTableMetaData(): ITableMetadata
    {
        return $this->current()->getTableMetaData();
    }

    /**
     * Returns the current table.
     */
    public function current(): ITable
    {
        $tableName = current($this->tableNames);

        return $this->dataSet->getTable($tableName);
    }

    /**
     * Returns the name of the current table.
     */
    public function key(): string
    {
        return $this->current()->getTableMetaData()->getTableName();
    }

    /**
     * advances to the next element.
     */
    public function next(): void
    {
        if ($this->reverse) {
            prev($this->tableNames);
        } else {
            next($this->tableNames);
        }
    }

    /**
     * Rewinds to the first element
     */
    public function rewind(): void
    {
        if ($this->reverse) {
            end($this->tableNames);
        } else {
            reset($this->tableNames);
        }
    }

    /**
     * Returns true if the current index is valid
     */
    public function valid(): bool
    {
        return current($this->tableNames) !== false;
    }
}
