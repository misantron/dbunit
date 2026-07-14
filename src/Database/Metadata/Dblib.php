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
 * Provides functionality to retrieve meta data from an Dblib (SQL Server) database.
 */
class Dblib extends AbstractMetadata
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

    protected array $columns = [];

    protected array $keys = [];

    /**
     * Returns an array containing the names of all the tables in the database.
     */
    public function getTableNames(): array
    {
        $tableNames = [];

        $query = 'SELECT name
                    FROM sys.tables
                   ORDER BY name';

        $result = $this->pdo->query($query);

        while ($tableName = $result->fetchColumn(0)) {
            $tableNames[] = $tableName;
        }

        return $tableNames;
    }

    /**
     * Returns an array containing the names of all the columns in the
     * $tableName table,
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
     */
    public function getTablePrimaryKeys(string $tableName): array
    {
        if (!isset($this->keys[$tableName])) {
            $this->loadColumnInfo($tableName);
        }

        return $this->keys[$tableName];
    }

    /**
     * Loads column info from a sql server database.
     */
    protected function loadColumnInfo(string $tableName): void
    {
        $query = "SELECT name
			FROM sys.columns
		   WHERE object_id = OBJECT_ID('" . $tableName . "')
		   ORDER BY column_id";

        $result = $this->pdo->query($query);

        while ($columnName = $result->fetchColumn(0)) {
            $this->columns[$tableName][] = $columnName;
        }

        $keyQuery = "SELECT COL_NAME(ic.OBJECT_ID,ic.column_id) AS ColumnName
			FROM    sys.indexes AS i INNER JOIN 
				sys.index_columns AS ic ON  i.OBJECT_ID = ic.OBJECT_ID
						        AND i.index_id = ic.index_id
			WHERE   i.is_primary_key = 1 AND OBJECT_NAME(ic.OBJECT_ID) = '" . $tableName . "'";

        $result = $this->pdo->query($keyQuery);

        while ($columnName = $result->fetchColumn(0)) {
            $this->keys[$tableName][] = $columnName;
        }
    }
}
