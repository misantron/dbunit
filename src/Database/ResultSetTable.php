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
use PHPUnit\DbUnit\DataSet\DefaultTableMetadata;

/**
 * Provides the functionality to represent a database result set as a DBUnit table.
 *
 * @deprecated The \PHPUnit\DbUnit\DataSet\QueryTable should be used instead
 * @see        \PHPUnit\DbUnit\DataSet\QueryTable
 * @see        \PHPUnit\DbUnit\DataSet\QueryDataSet
 */
class ResultSetTable extends AbstractTable
{
    /**
     * Creates a new result set table.
     *
     * @param string        $tableName
     */
    public function __construct($tableName, \PDOStatement $pdoStatement)
    {
        $this->data = $pdoStatement->fetchAll(\PDO::FETCH_ASSOC);

        $columns = \count($this->data) ? array_keys($this->data[0]) : [];

        $this->setTableMetaData(new DefaultTableMetadata($tableName, $columns));
    }
}
