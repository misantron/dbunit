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
 * Provides basic functionality for table meta data.
 */
abstract class AbstractTableMetadata implements ITableMetadata
{
    /**
     * The names of all columns in the table.
     */
    protected array $columns;

    /**
     * The names of all the primary keys in the table.
     */
    protected array $primaryKeys;

    protected string $tableName;

    /**
     * Returns the names of the columns in the table.
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Returns the names of the primary key columns in the table.
     */
    public function getPrimaryKeys(): array
    {
        return $this->primaryKeys;
    }

    /**
     * Returns the name of the table.
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * Asserts that the given tableMetaData matches this tableMetaData.
     */
    public function matches(ITableMetadata $other): bool
    {
        return $this->getTableName() === $other->getTableName() && $this->getColumns() === $other->getColumns();
    }
}
