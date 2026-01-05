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

/**
 * Allows for replacing arbitrary strings in your data sets with other values.
 *
 * @todo When setTableMetaData() is taken out of the AbstractTable this class should extend AbstractTable.
 */
class ReplacementTable implements ITable
{
    /**
     * @var ITable
     */
    protected $table;

    /**
     * @var array
     */
    protected $fullReplacements;

    /**
     * @var array
     */
    protected $subStrReplacements;

    /**
     * Creates a new replacement table
     */
    public function __construct(ITable $table, array $fullReplacements = [], array $subStrReplacements = [])
    {
        $this->table = $table;
        $this->fullReplacements = $fullReplacements;
        $this->subStrReplacements = $subStrReplacements;
    }

    public function __toString(): string
    {
        $columns = $this->getTableMetaData()->getColumns();

        $lineSeparator = str_repeat('+----------------------', \count($columns)) . "+\n";
        $lineLength = \strlen($lineSeparator) - 1;

        $tableString = $lineSeparator;
        $tableString .= '| ' . str_pad($this->getTableMetaData()->getTableName(), $lineLength - 4, ' ', STR_PAD_RIGHT) . " |\n";
        $tableString .= $lineSeparator;
        $tableString .= $this->rowToString($columns);
        $tableString .= $lineSeparator;

        $rowCount = $this->getRowCount();

        for ($i = 0; $i < $rowCount; $i++) {
            $values = [];

            foreach ($columns as $columnName) {
                $values[] = $this->getValue($i, $columnName);
            }

            $tableString .= $this->rowToString($values);
            $tableString .= $lineSeparator;
        }

        return "\n" . $tableString . "\n";
    }

    /**
     * Adds a new full replacement
     *
     * Full replacements will only replace values if the FULL value is a match
     */
    public function addFullReplacement(string $value, ?string $replacement): void
    {
        $this->fullReplacements[$value] = $replacement;
    }

    /**
     * Adds a new substr replacement
     *
     * Substr replacements will replace all occurances of the substr in every column
     *
     * @param string $value
     * @param string $replacement
     */
    public function addSubStrReplacement($value, $replacement): void
    {
        $this->subStrReplacements[$value] = $replacement;
    }

    /**
     * Returns the table's meta data.
     */
    public function getTableMetaData(): ITableMetadata
    {
        return $this->table->getTableMetaData();
    }

    /**
     * Returns the number of rows in this table.
     */
    public function getRowCount(): int
    {
        return $this->table->getRowCount();
    }

    /**
     * Returns the value for the given column on the given row.
     *
     *
     * @return mixed
     */
    public function getValue(int $row, string $column): mixed
    {
        return $this->getReplacedValue($this->table->getValue($row, $column));
    }

    /**
     * Returns the an associative array keyed by columns for the given row.
     *
     *
     */
    public function getRow(int $row): array
    {
        $row = $this->table->getRow($row);

        return array_map($this->getReplacedValue(...), $row);
    }

    /**
     * Asserts that the given table matches this table.
     *
     *
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
                        return false;
                    }
                } elseif ($thisValue !== $otherValue) {
                    return false;
                }
            }
        }

        return true;
    }

    protected function rowToString(array $row): string
    {
        $rowString = '';

        foreach ($row as $value) {
            if ($value === null) {
                $value = 'NULL';
            }

            $rowString .= '| ' . str_pad(substr($value, 0, 20), 20, ' ', STR_PAD_BOTH) . ' ';
        }

        return $rowString . "|\n";
    }

    protected function getReplacedValue($value)
    {
        if (\is_scalar($value) && \array_key_exists((string) $value, $this->fullReplacements)) {
            return $this->fullReplacements[$value];
        }

        if (isset($value) && \count($this->subStrReplacements)) {
            return str_replace(
                array_keys($this->subStrReplacements),
                array_values($this->subStrReplacements),
                $value
            );
        }

        return $value;
    }
}
