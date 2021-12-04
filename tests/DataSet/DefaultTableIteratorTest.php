<?php

declare(strict_types=1);

namespace PHPUnit\DbUnit\Tests\DataSet;

use PHPUnit\DbUnit\DataSet\DefaultTable;
use PHPUnit\DbUnit\DataSet\DefaultTableIterator;
use PHPUnit\DbUnit\DataSet\DefaultTableMetadata;
use PHPUnit\Framework\TestCase;

class DefaultTableIteratorTest extends TestCase
{
    public function testKey(): void
    {
        $tables = [
            new DefaultTable(
                new DefaultTableMetadata(
                    'table1',
                    ['id', 'column11', 'column12', 'column13'],
                    ['id']
                )
            ),
            new DefaultTable(
                new DefaultTableMetadata(
                    'table2',
                    ['id', 'column21', 'column22', 'column23'],
                    ['id']
                )
            ),
        ];

        $iterator = new DefaultTableIterator($tables);

        self::assertSame('table1', $iterator->key());

        $iterator->next();

        self::assertSame('table2', $iterator->key());
    }
}
