<?php

declare(strict_types=1);

/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\DbUnit\Database;

/**
 * Provides access to a database instance as a data set.
 */
class FilteredDataSet extends DataSet
{
    /**
     * Creates a new dataset using the given database connection.
     */
    public function __construct(
        Connection $databaseConnection,
        protected array $tableNames
    ) {
        parent::__construct($databaseConnection);
    }

    /**
     * Returns a list of table names for the database
     */
    public function getTableNames(): array
    {
        return $this->tableNames;
    }
}
