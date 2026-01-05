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
 * Updates the rows in a given dataset using primary key columns.
 */
class Update extends RowBased
{
    protected string $operationName = 'UPDATE';

    protected function buildOperationQuery(ITableMetadata $databaseTableMetaData, ITable $table, Connection $connection): string
    {
        $keys = $databaseTableMetaData->getPrimaryKeys();
        $columns = $table->getTableMetaData()->getColumns();
        $whereStatement = 'WHERE ' . implode(' AND ', $this->buildPreparedColumnArray($keys, $connection));
        $setStatement = 'SET ' . implode(', ', $this->buildPreparedColumnArray($columns, $connection));

        return "
            UPDATE {$connection->quoteSchemaObject($table->getTableMetaData()->getTableName())}
            {$setStatement}
            {$whereStatement}
        ";
    }

    protected function buildOperationArguments(ITableMetadata $databaseTableMetaData, ITable $table, $row): array
    {
        $args = [];

        foreach ($table->getTableMetaData()->getColumns() as $columnName) {
            $args[] = $table->getValue($row, $columnName);
        }

        foreach ($databaseTableMetaData->getPrimaryKeys() as $columnName) {
            $args[] = $table->getValue($row, $columnName);
        }

        return $args;
    }

    protected function disablePrimaryKeys(ITableMetadata $databaseTableMetaData, ITable $table, Connection $connection): bool
    {
        return \count($databaseTableMetaData->getPrimaryKeys()) > 0;
    }
}
