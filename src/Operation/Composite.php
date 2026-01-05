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
use PHPUnit\DbUnit\Exception\InvalidArgumentException;

/**
 * This class facilitates combining database operations. To create a composite
 * operation pass an array of classes that implement
 * PHPUnit\DbUnit\Operation\Operation and they will be
 * executed in that order against all data sets.
 */
class Composite implements Operation
{
    /**
     * @var Operation[]
     */
    protected array $operations = [];

    /**
     * @param Operation[] $operations
     */
    public function __construct(array $operations)
    {
        foreach ($operations as $operation) {
            if (!$operation instanceof Operation) {
                throw new InvalidArgumentException(
                    'Only database operation instances can be passed to a composite database operation.'
                );
            }
            $this->operations[] = $operation;
        }
    }

    public function execute(Connection $connection, IDataSet $dataSet): void
    {
        try {
            foreach ($this->operations as $operation) {
                $operation->execute($connection, $dataSet);
            }
        } catch (Exception $e) {
            throw new Exception(
                "COMPOSITE[{$e->getOperation()}]",
                $e->getQuery(),
                $e->getArgs(),
                $e->getTable(),
                $e->getError()
            );
        }
    }
}
