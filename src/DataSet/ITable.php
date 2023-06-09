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
interface ITable extends \Stringable
{
    /**
     * Returns the table's meta data.
     *
     * @return ITableMetadata
     */
    public function getTableMetaData(): ITableMetadata;

    /**
     * Returns the number of rows in this table.
     *
     * @return int
     */
    public function getRowCount(): int;

    /**
     * Returns the value for the given column on the given row.
     *
     * @param int $row
     * @param string $column
     */
    public function getValue(int $row, string $column);

    /**
     * Returns the an associative array keyed by columns for the given row.
     *
     * @param int $row
     *
     * @return array
     */
    public function getRow(int $row): array;

    /**
     * Asserts that the given table matches this table.
     *
     * @param ITable $other
     *
     * @return bool
     */
    public function matches(self $other): bool;
}
