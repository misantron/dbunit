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
 * Provides functionality to retrieve meta data from a Microsoft SQL Server database.
 */
class SqlSrv extends AbstractMetadata
{
    /**
     * No character used to quote schema objects.
     *
     * @var string
     */
    protected $schemaObjectQuoteChar = '';

    /**
     * The command used to perform a TRUNCATE operation.
     *
     * @var string
     */
    protected $truncateCommand = 'TRUNCATE TABLE';

    /**
     * Returns an array containing the names of all the tables in the database.
     */
    public function getTableNames(): array
    {
        $query = "SELECT name
                    FROM sysobjects
                   WHERE type='U'";

        $statement = $this->pdo->query($query);

        $tableNames = [];

        while ($tableName = $statement->fetchColumn()) {
            $tableNames[] = $tableName;
        }

        return $tableNames;
    }

    /**
     * Returns an array containing the names of all the columns in the
     * $tableName table.
     *
     *
     */
    public function getTableColumns(string $tableName): array
    {
        $query = "SELECT c.name
                    FROM syscolumns c
               LEFT JOIN sysobjects o ON c.id = o.id
                   WHERE o.name = '{$tableName}'";

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
     */
    public function getTablePrimaryKeys(string $tableName): array
    {
        $query = sprintf("EXEC sp_statistics '%s'", $tableName);
        $statement = $this->pdo->query($query);
        $statement->setFetchMode(\PDO::FETCH_ASSOC);

        $columnNames = [];

        while ($column = $statement->fetch()) {
            if ((int) $column['TYPE'] === 1) {
                $columnNames[] = $column['COLUMN_NAME'];
            }
        }

        return $columnNames;
    }

    /**
     * Allow overwriting identities for the given table.
     */
    public function disablePrimaryKeys(string $tableName): void
    {
        try {
            $query = sprintf('SET IDENTITY_INSERT %s ON', $tableName);
            $this->pdo->exec($query);
        } catch (\PDOException) {
            // ignore the error here - can happen if primary key is not an identity
        }
    }

    /**
     * Reenable auto creation of identities for the given table.
     */
    public function enablePrimaryKeys(string $tableName): void
    {
        try {
            $query = sprintf('SET IDENTITY_INSERT %s OFF', $tableName);
            $this->pdo->exec($query);
        } catch (\PDOException) {
            // ignore the error here - can happen if primary key is not an identity
        }
    }
}
