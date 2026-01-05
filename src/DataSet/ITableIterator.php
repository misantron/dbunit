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
interface ITableIterator extends \Iterator
{
    /**
     * Returns the current table.
     */
    public function getTable(): ITable;

    /**
     * Returns the current table's meta data.
     */
    public function getTableMetaData(): ITableMetadata;
}
