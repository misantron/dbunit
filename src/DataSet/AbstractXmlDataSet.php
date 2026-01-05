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
use PHPUnit\DbUnit\Exception\RuntimeException;

/**
 * The default implementation of a data set.
 */
abstract class AbstractXmlDataSet extends AbstractDataSet
{
    protected array $tables;

    protected \SimpleXmlElement|false $xmlFileContents;

    public function __construct(string $xmlFile)
    {
        if (!is_file($xmlFile)) {
            throw new InvalidArgumentException('Could not find xml file: ' . $xmlFile);
        }

        libxml_use_internal_errors(true);

        $this->xmlFileContents = simplexml_load_string(
            data: file_get_contents($xmlFile),
            options: LIBXML_COMPACT
        );

        if ($this->xmlFileContents === false) {
            $errors = array_map(static fn (\LibXMLError $error): string => trim($error->message), libxml_get_errors());

            libxml_clear_errors();

            throw new RuntimeException(implode(', ', $errors));
        }

        $tableColumns = [];
        $tableValues = [];

        $this->getTableInfo($tableColumns, $tableValues);
        $this->createTables($tableColumns, $tableValues);
    }

    /**
     * Reads the simple xml object and creates the appropriate tables and meta
     * data for this dataset.
     */
    abstract protected function getTableInfo(array &$tableColumns, array &$tableValues);

    protected function createTables(array $tableColumns, array $tableValues): void
    {
        foreach ($tableValues as $tableName => $values) {
            $table = $this->getOrCreateTable($tableName, $tableColumns[$tableName]);

            foreach ($values as $value) {
                $table->addRow($value);
            }
        }
    }

    /**
     * Returns the table with the matching name. If the table does not exist
     * an empty one is created.
     */
    protected function getOrCreateTable(string $tableName, array $tableColumns): DefaultTable
    {
        if (empty($this->tables[$tableName])) {
            $tableMetaData = new DefaultTableMetadata($tableName, $tableColumns);
            $this->tables[$tableName] = new DefaultTable($tableMetaData);
        }

        return $this->tables[$tableName];
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
