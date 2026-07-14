<?php

/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit\Database;

use PHPUnit\DbUnit\DataSet\AbstractTable;
use PHPUnit\DbUnit\DataSet\ITableMetadata;

/**
 * Provides the functionality to represent a database table.
 */
class Table extends AbstractTable
{
    /**
     * Creates a new database table object.
     */
    public function __construct(ITableMetadata $tableMetaData, Connection $databaseConnection)
    {
        $this->setTableMetaData($tableMetaData);

        $pdoStatement = $databaseConnection
            ->getConnection()
            ->query(DataSet::buildTableSelect($tableMetaData, $databaseConnection));

        $this->data = $pdoStatement->fetchAll(\PDO::FETCH_ASSOC);
    }
}
