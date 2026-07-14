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

namespace PHPUnit\DbUnit\DataSet;

/**
 * The default implementation of a data set.
 */
class DefaultDataSet extends AbstractDataSet
{
    public function __construct(
        /**
         * An array of ITable objects.
         */
        protected array $tables = []
    ) {
    }

    /**
     * Adds a table to the dataset.
     */
    public function addTable(ITable $table): void
    {
        $this->tables[] = $table;
    }

    /**
     * Creates an iterator over the tables in the data set. If $reverse is
     * true a reverse iterator will be returned.
     */
    protected function createIterator(bool $reverse = false): ITableIterator
    {
        return new DefaultTableIterator($this->tables, $reverse);
    }
}
