<?php

/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit\DataSet;

use PHPUnit\DbUnit\Exception\InvalidArgumentException;

/**
 * Creates CsvDataSets.
 *
 * You can incrementally add CSV files as tables to your datasets
 */
class CsvDataSet extends AbstractDataSet
{
    protected array $tables = [];

    /**
     * Creates a new CSV dataset
     *
     * You can pass in the parameters for how csv files will be read.
     */
    public function __construct(
        protected string $delimiter = ',',
        protected string $enclosure = '"',
        protected string $escape = '"',
    ) {
    }

    /**
     * Adds a table to the dataset
     *
     * The table will be given the passed name. $csvFile should be a path to
     * a valid csv file (based on the arguments passed to the constructor.)
     */
    public function addTable(string $tableName, string $csvFile): void
    {
        if (!is_file($csvFile)) {
            throw new InvalidArgumentException('Could not find csv file: ' . $csvFile);
        }

        if (!is_readable($csvFile)) {
            throw new InvalidArgumentException('Could not read csv file: ' . $csvFile);
        }

        $file = new \SplFileObject($csvFile, 'rb');
        $file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY | \SplFileObject::DROP_NEW_LINE);
        $file->setCsvControl(separator: $this->delimiter, enclosure: $this->enclosure, escape: $this->escape);

        $columns = $this->getCsvRow($file);
        if ($columns === null) {
            throw new InvalidArgumentException('Could not determine the headers from the given file ' . $csvFile);
        }

        $metaData = new DefaultTableMetadata($tableName, $columns);
        $table = new DefaultTable($metaData);

        $rowNumber = 1;
        $columnsCount = \count($columns);

        while (!$file->eof()) {
            $row = $this->getCsvRow($file);
            if ($row === null) {
                continue;
            }

            if ($columnsCount !== \count($row)) {
                throw new InvalidArgumentException(sprintf('Row no. %d in csv file %s should have an equal number of elements as table %s', $rowNumber, $csvFile, $tableName));
            }

            $table->addRow(array_combine($columns, $row));
            ++$rowNumber;
        }

        $this->tables[$tableName] = $table;
    }

    /**
     * Creates an iterator over the tables in the data set. If $reverse is
     * true a reverse iterator will be returned.
     */
    protected function createIterator(bool $reverse = false): ITableIterator
    {
        return new DefaultTableIterator($this->tables, $reverse);
    }

    /**
     * Returns a row from the csv file in an indexed array.
     */
    private function getCsvRow(\SplFileObject $file): ?array
    {
        $row = $file->fgetcsv(separator: $this->delimiter, enclosure: $this->enclosure, escape: $this->escape);
        if ($row === false) {
            return null;
        }

        return $row;
    }
}
