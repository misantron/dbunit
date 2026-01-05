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
 * Provides functionality to retrieve meta data from a Firebird database.
 */
class Firebird extends AbstractMetadata
{
    protected array $columns = [];

    protected array $keys = [];

    /**
     * The command used to perform a TRUNCATE operation.
     *
     * @var string
     */
    protected $truncateCommand = 'DELETE FROM';

    /**
     * Returns an array containing the names of all the tables in the database.
     *
     * @return array
     */
    public function getTableNames(): array
    {
        $query = '
            select
              RDB$RELATION_NAME as TABLE_NAME
            from RDB$RELATIONS
            where
              ((RDB$RELATION_TYPE = 0) or
               (RDB$RELATION_TYPE is null)) and
              (RDB$SYSTEM_FLAG = 0)
            order by (RDB$RELATION_NAME)
        ';

        $statement = $this->pdo->prepare($query);
        $statement->execute([$this->getSchema()]);

        $tableNames = [];

        while ($tableName = $statement->fetchColumn(0)) {
            $tableNames[] = $tableName;
        }

        return $tableNames;
    }

    /**
     * Returns an array containing the names of all the columns in the
     * $tableName table,
     *
     * @param string $tableName
     *
     * @return array
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
     * @param string $tableName
     *
     * @return array
     */
    public function getTablePrimaryKeys(string $tableName): array
    {
        if (!isset($this->keys[$tableName])) {
            $this->loadColumnInfo($tableName);
        }

        return $this->keys[$tableName];
    }

    /**
     * Returns the schema for the connection.
     *
     * @return string
     */
    public function getSchema(): string
    {
        if (empty($this->schema)) {
            return 'public';
        }

        return $this->schema;
    }

    /**
     * Returns true if the rdbms allows cascading
     *
     * @return bool
     */
    public function allowsCascading(): bool
    {
        return false;
    }

    /**
     * Returns a quoted schema object. (table name, column name, etc)
     *
     * @param string $object
     *
     * @return string
     */
    public function quoteSchemaObject(string $object): string
    {
        return $object; //firebird does not allow object quoting
    }

    /**
     * Loads column info from a database table.
     *
     * @param string $tableName
     */
    protected function loadColumnInfo(string $tableName): void
    {
        $this->columns[$tableName] = [];
        $this->keys[$tableName] = [];

        $columnQuery = '
            select
              rf.RDB$FIELD_NAME as COLUMN_NAME,
              rf.RDB$FIELD_POSITION as ORDINAL_POSITION
            from RDB$RELATION_FIELDS as rf
            where
              upper(RDB$RELATION_NAME) = upper(?)
            order by
              ORDINAL_POSITION

        ';

        $columnStatement = $this->pdo->prepare($columnQuery);
        $columnStatement->execute([$tableName]);

        while ($columName = $columnStatement->fetchColumn(0)) {
            $this->columns[$tableName][] = $columName;
        }

        $keyQuery = "
            select
              idseg.rdb\$field_name as COLUMN_NAME,
              idseg.rdb\$field_position as ORDINAL_POSITION,
              rc.rdb\$relation_name as tablename,
              rc.rdb\$constraint_name as pk_name
            from
              RDB\$RELATION_CONSTRAINTS AS rc
                left join
              rdb\$index_segments as idseg on
                (rc.rdb\$index_name = idseg.rdb\$index_name)
            where
              rc.RDB\$CONSTRAINT_TYPE = 'PRIMARY KEY'
              and upper(rc.RDB\$RELATION_NAME) = upper(?)
            order by
              rc.rdb\$constraint_name, idseg.rdb\$field_position
        ";

        $keyStatement = $this->pdo->prepare($keyQuery);
        $keyStatement->execute([$tableName]);

        while ($columName = $keyStatement->fetchColumn(0)) {
            $this->keys[$tableName][] = $columName;
        }
    }
}
