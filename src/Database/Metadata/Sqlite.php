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

use PDO;

/**
 * Provides functionality to retrieve meta data from an Sqlite database.
 */
class Sqlite extends AbstractMetadata
{
    protected array $columns = [];

    protected array $keys = [];

    protected $truncateCommand = 'DELETE FROM';

    /**
     * Returns an array containing the names of all the tables in the database.
     */
    public function getTableNames(): array
    {
        $query = "
            SELECT name
            FROM sqlite_master
            WHERE
                type='table' AND
                name <> 'sqlite_sequence'
            ORDER BY name
        ";

        $result = $this->pdo->query($query);

        $tableNames = [];

        while ($tableName = $result->fetchColumn()) {
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
        $query = sprintf("PRAGMA table_info('%s')", $tableName);
        $statement = $this->pdo->query($query);

        $this->columns[$tableName] = [];
        $this->keys[$tableName] = [];

        while ($columnData = $statement->fetch(PDO::FETCH_NUM)) {
            $this->columns[$tableName][] = $columnData[1];

            if ((int) $columnData[5] !== 0) {
                $this->keys[$tableName][] = $columnData[1];
            }
        }
    }
}
