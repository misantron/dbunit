<?php

/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit\Operation;

use PHPUnit\DbUnit\Database\Connection;
use PHPUnit\DbUnit\DataSet\ITable;
use PHPUnit\DbUnit\DataSet\ITableMetadata;

/**
 * This class provides functionality for inserting rows from a dataset into a database.
 */
class Insert extends RowBased
{
    protected string $operationName = 'INSERT';

    protected function buildOperationQuery(ITableMetadata $databaseTableMetaData, ITable $table, Connection $connection): string
    {
        $columnCount = \count($table->getTableMetaData()->getColumns());

        if ($columnCount === 0) {
            return '';
        }

        $placeHolders = implode(', ', array_fill(0, $columnCount, '?'));

        $columns = '';

        foreach ($table->getTableMetaData()->getColumns() as $column) {
            $columns .= $connection->quoteSchemaObject($column) . ', ';
        }

        $columns = substr($columns, 0, -2);

        return "
                INSERT INTO {$connection->quoteSchemaObject($table->getTableMetaData()->getTableName())}
                ({$columns})
                VALUES
                ({$placeHolders})
            ";
    }

    protected function buildOperationArguments(ITableMetadata $databaseTableMetaData, ITable $table, $row): array
    {
        $args = [];

        foreach ($table->getTableMetaData()->getColumns() as $columnName) {
            $args[] = $table->getValue($row, $columnName);
        }

        return $args;
    }

    protected function disablePrimaryKeys(ITableMetadata $databaseTableMetaData, ITable $table, Connection $connection): bool
    {
        return \count($databaseTableMetaData->getPrimaryKeys()) > 0;
    }
}
