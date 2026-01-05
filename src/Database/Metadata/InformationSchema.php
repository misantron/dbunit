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
 * Provides functionality to retrieve meta data from a database with information_schema support.
 */
class InformationSchema extends AbstractMetadata
{
    protected array $columns = [];

    protected array $keys = [];

    /**
     * Returns an array containing the names of all the tables in the database.
     */
    public function getTableNames(): array
    {
        $query = "
            SELECT DISTINCT
                TABLE_NAME
            FROM INFORMATION_SCHEMA.TABLES
            WHERE
                TABLE_TYPE='BASE TABLE' AND
                TABLE_SCHEMA = ?
            ORDER BY TABLE_NAME
        ";

        $statement = $this->pdo->prepare($query);
        $statement->execute([$this->getSchema()]);

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
        if (!isset($this->columns[$tableName])) {
            $this->loadColumnInfo($tableName);
        }

        return $this->columns[$tableName];
    }

    /**
     * Returns an array containing the names of all the primary key columns in
     * the $tableName table.
     *
     *
     */
    public function getTablePrimaryKeys(string $tableName): array
    {
        if (!isset($this->keys[$tableName])) {
            $this->loadColumnInfo($tableName);
        }

        return $this->keys[$tableName];
    }

    /**
     * Loads column info from a sqlite database.
     */
    protected function loadColumnInfo(string $tableName): void
    {
        $this->columns[$tableName] = [];
        $this->keys[$tableName] = [];

        $columnQuery = '
            SELECT DISTINCT
                COLUMN_NAME
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE
                TABLE_NAME = ? AND
                TABLE_SCHEMA = ?
            ORDER BY ORDINAL_POSITION
        ';

        $columnStatement = $this->pdo->prepare($columnQuery);
        $columnStatement->execute([$tableName, $this->getSchema()]);

        while ($columnName = $columnStatement->fetchColumn()) {
            $this->columns[$tableName][] = $columnName;
        }

        $keyQuery = "
            SELECT
                KCU.COLUMN_NAME
            FROM
                INFORMATION_SCHEMA.TABLE_CONSTRAINTS as TC,
                INFORMATION_SCHEMA.KEY_COLUMN_USAGE as KCU
            WHERE
                TC.CONSTRAINT_NAME = KCU.CONSTRAINT_NAME AND
                TC.TABLE_NAME = KCU.TABLE_NAME AND
                TC.TABLE_SCHEMA = KCU.TABLE_SCHEMA AND
                TC.CONSTRAINT_TYPE = 'PRIMARY KEY' AND
                TC.TABLE_NAME = ? AND
                TC.TABLE_SCHEMA = ?
            ORDER BY
                KCU.ORDINAL_POSITION ASC
        ";

        $keyStatement = $this->pdo->prepare($keyQuery);
        $keyStatement->execute([$tableName, $this->getSchema()]);

        while ($columnName = $keyStatement->fetchColumn()) {
            $this->keys[$tableName][] = $columnName;
        }
    }
}
