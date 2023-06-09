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

/**
 * Provides a basic interface for creating and reading data from data sets.
 */
interface IDataSet extends \IteratorAggregate, \Stringable
{
    /**
     * Returns an array of table names contained in the dataset.
     *
     * @return array
     */
    public function getTableNames(): array;

    /**
     * Returns a table meta data object for the given table.
     *
     * @param string $tableName
     *
     * @return ITableMetadata
     */
    public function getTableMetaData(string $tableName): ITableMetadata;

    /**
     * Returns a table object for the given table.
     *
     * @param string $tableName
     *
     * @return ITable
     */
    public function getTable(string $tableName): ITable;

    /**
     * Returns a reverse iterator for all table objects in the given dataset.
     *
     * @return ITableIterator|ITable[]
     */
    public function getReverseIterator(): ITableIterator;

    /**
     * Asserts that the given data set matches this data set.
     *
     * @param IDataSet $other
     *
     * @return bool
     */
    public function matches(self $other): bool;
}
