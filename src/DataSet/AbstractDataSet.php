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

use PHPUnit\DbUnit\Exception\InvalidArgumentException;

/**
 * Implements the basic functionality of data sets.
 */
abstract class AbstractDataSet implements IDataSet
{
    public function __toString()
    {
        /** @var ITable[] $iterator */
        $iterator = $this->getIterator();

        $tables = [];
        foreach ($iterator as $table) {
            $tables[] = $table->getTableMetadata()->getTableName();
        }

        return '[' . implode(',', $tables) . ']';
    }

    /**
     * Returns an array of table names contained in the dataset.
     *
     * @return array
     */
    public function getTableNames(): array
    {
        $tableNames = [];

        foreach ($this->getIterator() as $table) {
            $tableNames[] = $table->getTableMetaData()->getTableName();
        }

        return $tableNames;
    }

    /**
     * Returns a table meta data object for the given table.
     *
     * @param string $tableName
     *
     * @return ITableMetadata
     */
    public function getTableMetaData(string $tableName): ITableMetadata
    {
        return $this->getTable($tableName)->getTableMetaData();
    }

    /**
     * Returns a table object for the given table.
     *
     * @param string $tableName
     *
     * @return ITable
     */
    public function getTable(string $tableName): ITable
    {
        foreach ($this->getIterator() as $table) {
            if ($table->getTableMetaData()->getTableName() === $tableName) {
                return $table;
            }
        }

        throw new InvalidArgumentException("{$tableName} is not a table in the current database.");
    }

    /**
     * Returns an iterator for all table objects in the given dataset.
     *
     * @return ITableIterator
     */
    public function getIterator(): ITableIterator
    {
        return $this->createIterator();
    }

    /**
     * Returns a reverse iterator for all table objects in the given dataset.
     *
     * @return ITableIterator
     */
    public function getReverseIterator(): ITableIterator
    {
        return $this->createIterator(true);
    }

    /**
     * Asserts that the given data set matches this data set.
     *
     * @param IDataSet $other
     *
     * @return bool
     */
    public function matches(IDataSet $other): bool
    {
        $thisTableNames = $this->getTableNames();
        $otherTableNames = $other->getTableNames();

        sort($thisTableNames);
        sort($otherTableNames);

        if ($thisTableNames !== $otherTableNames) {
            return false;
        }

        foreach ($thisTableNames as $tableName) {
            $table = $this->getTable($tableName);

            if (!$table->matches($other->getTable($tableName))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Creates an iterator over the tables in the data set. If $reverse is
     * true a reverse iterator will be returned.
     *
     * @param bool $reverse
     *
     * @return ITableIterator
     */
    abstract protected function createIterator(bool $reverse = false): ITableIterator;
}
