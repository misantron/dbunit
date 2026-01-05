<?php

/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit\Operation;

use PHPUnit\DbUnit\Database\Connection;
use PHPUnit\DbUnit\DataSet\IDataSet;

/**
 * Executes a truncate against all tables in a dataset.
 */
class Truncate implements Operation
{
    private bool $useCascade = false;

    public function setCascade(bool $cascade = true): void
    {
        $this->useCascade = $cascade;
    }

    /**
     *
     * @throws \Throwable
     */
    public function execute(Connection $connection, IDataSet $dataSet): void
    {
        $truncateCommand = $connection->getTruncateCommand();

        foreach ($dataSet->getReverseIterator() as $table) {
            $query = sprintf('%s %s', $truncateCommand, $connection->quoteSchemaObject($table->getTableMetaData()->getTableName()));

            if ($this->useCascade && $connection->allowsCascading()) {
                $query .= ' CASCADE';
            }

            try {
                $this->disableForeignKeyChecksForMysql($connection);
                $connection->getConnection()->exec($query);
                $this->enableForeignKeyChecksForMysql($connection);
            } catch (\Throwable $e) {
                $this->enableForeignKeyChecksForMysql($connection);

                if ($e instanceof \PDOException) {
                    throw new Exception('TRUNCATE', $query, [], $table, $e->getMessage());
                }

                throw $e;
            }
        }
    }

    private function disableForeignKeyChecksForMysql(Connection $connection): void
    {
        if ($this->isMysql($connection)) {
            $connection->getConnection()->exec('SET @PHPUNIT_OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS');
            $connection->getConnection()->exec('SET FOREIGN_KEY_CHECKS = 0');
        }
    }

    private function enableForeignKeyChecksForMysql(Connection $connection): void
    {
        if ($this->isMysql($connection)) {
            $connection->getConnection()->exec('SET FOREIGN_KEY_CHECKS=@PHPUNIT_OLD_FOREIGN_KEY_CHECKS');
        }
    }

    private function isMysql(Connection $connection): bool
    {
        return $connection->getConnection()->getAttribute(\PDO::ATTR_DRIVER_NAME) === 'mysql';
    }
}
