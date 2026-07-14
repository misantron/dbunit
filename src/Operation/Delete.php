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
 * Deletes the rows in a given dataset using primary key columns.
 */
class Delete extends RowBased
{
    protected string $operationName = 'DELETE';

    protected int $iteratorDirection = self::ITERATOR_TYPE_REVERSE;

    protected function buildOperationQuery(ITableMetadata $databaseTableMetaData, ITable $table, Connection $connection): string
    {
        $keys = $databaseTableMetaData->getPrimaryKeys();

        $whereStatement = 'WHERE ' . implode(' AND ', $this->buildPreparedColumnArray($keys, $connection));

        return "
            DELETE FROM {$connection->quoteSchemaObject($table->getTableMetaData()->getTableName())}
            {$whereStatement}
        ";
    }

    protected function buildOperationArguments(ITableMetadata $databaseTableMetaData, ITable $table, $row): array
    {
        $args = [];

        foreach ($databaseTableMetaData->getPrimaryKeys() as $columnName) {
            $args[] = $table->getValue($row, $columnName);
        }

        return $args;
    }
}
