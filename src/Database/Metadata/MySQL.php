<?php

/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit\Database\Metadata;

/**
 * Provides functionality to retrieve meta data from a MySQL database.
 */
class MySQL extends AbstractMetadata
{
    protected $schemaObjectQuoteChar = '`';

    /**
     * Returns an array containing the names of all the tables in the database.
     */
    public function getTableNames(): array
    {
        $query = 'SHOW TABLES';
        $statement = $this->pdo->query($query);

        $tableNames = [];

        while ($tableName = $statement->fetchColumn()) {
            $tableNames[] = $tableName;
        }

        return $tableNames;
    }

    /**
     * Returns an array containing the names of all the columns in the
     * $tableName table,
     *
     *
     */
    public function getTableColumns(string $tableName): array
    {
        $query = 'SHOW COLUMNS FROM ' . $this->quoteSchemaObject($tableName);
        $statement = $this->pdo->query($query);

        $columnNames = [];

        while ($columnName = $statement->fetchColumn()) {
            $columnNames[] = $columnName;
        }

        return $columnNames;
    }

    /**
     * Returns an array containing the names of all the primary key columns in
     * the $tableName table.
     *
     *
     */
    public function getTablePrimaryKeys(string $tableName): array
    {
        $query = 'SHOW INDEX FROM ' . $this->quoteSchemaObject($tableName);
        $statement = $this->pdo->query($query);
        $statement->setFetchMode(\PDO::FETCH_ASSOC);

        $columnNames = [];

        while ($column = $statement->fetch()) {
            if ($column['Key_name'] === 'PRIMARY') {
                $columnNames[] = $column['Column_name'];
            }
        }

        return $columnNames;
    }
}
