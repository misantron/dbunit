<?php

/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit\DataSet\Specification;

use PHPUnit\DbUnit\Database\DefaultConnection;
use PHPUnit\DbUnit\DatabaseListConsumer;
use PHPUnit\DbUnit\DataSet\DefaultDataSet;
use Webmozart\Assert\Assert;

/**
 * Creates DefaultDataSets based off of a spec string.
 *
 * This spec class requires a list of databases to be set to the object before
 * it can return a list of databases.
 *
 * The format of the spec string is as follows:
 *
 * <db label>:<schema>:<table name>:<sql>
 *
 * The db label should be equal to one of the keys in the array of databases
 * passed to setDatabases().
 *
 * The schema should be the primary schema you will be running the sql query
 * against.
 *
 * The table name should be set to what you would like the table name in the
 * dataset to be.
 *
 * The sql is the query you want to use to generate the table columns and data.
 * The column names in the table will be identical to the column aliases in the
 * query.
 */
class Query implements Specification, DatabaseListConsumer
{
    /**
     * @var array<string, string>
     */
    protected array $databases = [];

    /**
     * Sets the database for the spec
     */
    public function setDatabases(array $databases): void
    {
        $this->databases = $databases;
    }

    /**
     * Creates a Default Data Set with a query table from a data set spec.
     *
     *
     */
    public function getDataSet(string $dataSetSpec): DefaultDataSet
    {
        [$dbLabel, $schema, $table, $sql] = explode(':', $dataSetSpec, 4);

        Assert::stringNotEmpty($dbLabel);
        Assert::stringNotEmpty($schema);
        Assert::stringNotEmpty($table);
        Assert::stringNotEmpty($sql);

        $databaseInfo = $this->databases[$dbLabel] ?? null;

        Assert::stringNotEmpty($databaseInfo);

        $pdoRflc = new \ReflectionClass(\PDO::class);
        $pdo = $pdoRflc->newInstanceArgs(explode('|', $databaseInfo));

        Assert::notNull($pdo);

        $dbConnection = new DefaultConnection($pdo, $schema);
        $table = $dbConnection->createQueryTable($table, $sql);

        return new DefaultDataSet([$table]);
    }
}
