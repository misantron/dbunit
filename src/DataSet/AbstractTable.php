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
 * Provides a basic functionality for dbunit tables
 */
class AbstractTable implements ITable
{
    /**
     * @var ITableMetadata
     */
    protected $tableMetaData;

    /**
     * A 2-dimensional array containing the data for this table.
     *
     * @var array
     */
    protected $data;

    private ?ITable $other = null;

    public function __toString(): string
    {
        $columns = $this->getTableMetaData()
            ->getColumns();
        $count = \count($columns);

        // if count less than 0 (when table is empty), then set count to 1
        $count = $count > 0 ? $count : 1;

        $lineSeparator = str_repeat('+----------------------', $count) . "+\n";
        $lineLength = \strlen($lineSeparator) - 1;

        $tableString = $lineSeparator;
        $tblName = $this->getTableMetaData()
            ->getTableName();
        $tableString .= '| ' . str_pad($tblName, $lineLength - 4) . " |\n";
        $tableString .= $lineSeparator;
        $rows = $this->rowToString($columns);
        $tableString .= $rows === '' || $rows === '0' ? '' : $rows . $lineSeparator;

        $rowCount = $this->getRowCount();

        for ($i = 0; $i < $rowCount; $i++) {
            $values = [];

            foreach ($columns as $columnName) {
                if ($this->other instanceof ITable) {
                    try {
                        if ($this->getValue($i, $columnName) !== $this->other->getValue($i, $columnName)) {
                            $values[] = sprintf(
                                '%s != actual %s',
                                var_export($this->getValue($i, $columnName), true),
                                var_export($this->other->getValue($i, $columnName), true)
                            );
                        } else {
                            $values[] = $this->getValue($i, $columnName);
                        }
                    } catch (\InvalidArgumentException) {
                        $values[] = $this->getValue($i, $columnName) . ': no row';
                    }
                } else {
                    $values[] = $this->getValue($i, $columnName);
                }
            }

            $tableString .= $this->rowToString($values) . $lineSeparator;
        }

        return ($this->other instanceof ITable ? '(table diff enabled)' : '') . "\n" . $tableString . "\n";
    }

    /**
     * Returns the table's meta data.
     */
    public function getTableMetaData(): ITableMetadata
    {
        return $this->tableMetaData;
    }

    /**
     * Returns the number of rows in this table.
     */
    public function getRowCount(): int
    {
        return \count($this->data);
    }

    /**
     * Returns the value for the given column on the given row.
     *
     * @return mixed|string
     */
    public function getValue(int $row, string $column): mixed
    {
        if (!isset($this->data[$row]) || !\in_array($column, $this->getTableMetaData()->getColumns(), true)) {
            throw new InvalidArgumentException(
                sprintf('The given row (%d) and column (%s) do not exist in table %s', $row, $column, $this->getTableMetaData()->getTableName())
            );
        }

        $value = $this->data[$row][$column];

        return $value instanceof \SimpleXMLElement ? (string) $value : $value;
    }

    /**
     * Returns the an associative array keyed by columns for the given row.
     */
    public function getRow(int $row): array
    {
        if (!isset($this->data[$row])) {
            throw new InvalidArgumentException(
                sprintf('The given row (%d) does not exist in table %s', $row, $this->getTableMetaData()->getTableName())
            );
        }

        return $this->data[$row];
    }

    /**
     * Asserts that the given table matches this table.
     */
    public function matches(ITable $other): bool
    {
        $thisMetaData = $this->getTableMetaData();
        $otherMetaData = $other->getTableMetaData();

        if (!$thisMetaData->matches($otherMetaData) || $this->getRowCount() !== $other->getRowCount()) {
            return false;
        }

        $columns = $thisMetaData->getColumns();
        $rowCount = $this->getRowCount();

        for ($i = 0; $i < $rowCount; $i++) {
            foreach ($columns as $columnName) {
                $thisValue = $this->getValue($i, $columnName);
                $otherValue = $other->getValue($i, $columnName);

                if (is_numeric($thisValue) && is_numeric($otherValue)) {
                    if ($thisValue != $otherValue) {
                        $this->other = $other;

                        return false;
                    }
                } elseif ($thisValue !== $otherValue) {
                    $this->other = $other;

                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Checks if a given row is in the table
     */
    public function assertContainsRow(array $row): bool
    {
        return \in_array($row, $this->data, true);
    }

    /**
     * Sets the metadata for this table.
     *
     * @deprecated
     */
    protected function setTableMetaData(ITableMetadata $tableMetaData): void
    {
        $this->tableMetaData = $tableMetaData;
    }

    protected function rowToString(array $row): string
    {
        $rowString = '';

        foreach ($row as $value) {
            if ($value === null) {
                $value = 'NULL';
            }

            $value_str = mb_substr($value, 0, 20);

            // make str_pad act in multi byte manner
            $correction = \strlen($value_str) - mb_strlen($value_str);
            $rowString .= '| ' . str_pad($value_str, 20 + $correction, ' ', STR_PAD_BOTH) . ' ';
        }

        /** @see https://github.com/sebastianbergmann/dbunit/issues/195 */
        $rowString = $row === [] ? '' : $rowString . "|\n";

        return $rowString;
    }
}
