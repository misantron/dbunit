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
 * Provides a basic interface for retrieving metadata from a database.
 */
interface Metadata
{
    /**
     * Returns an array containing the names of all the tables in the database.
     */
    public function getTableNames(): array;

    /**
     * Returns an array containing the names of all the columns in the
     * $tableName table,
     */
    public function getTableColumns(string $tableName): array;

    /**
     * Returns an array containing the names of all the primary key columns in
     * the $tableName table.
     */
    public function getTablePrimaryKeys(string $tableName): array;

    /**
     * Returns the name of the default schema.
     */
    public function getSchema(): string;

    /**
     * Returns a quoted schema object. (table name, column name, etc)
     */
    public function quoteSchemaObject(string $object): string;

    /**
     * Returns true if the rdbms allows cascading
     */
    public function allowsCascading(): bool;

    /**
     * Disables primary keys if rdbms does not allow setting them otherwise
     */
    public function disablePrimaryKeys(string $tableName): void;

    /**
     * Reenables primary keys after they have been disabled
     */
    public function enablePrimaryKeys(string $tableName): void;
}
