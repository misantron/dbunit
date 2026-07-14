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
namespace PHPUnit\DbUnit\Operation;

use PHPUnit\DbUnit\DataSet\ITable;
use PHPUnit\DbUnit\Exception\RuntimeException;

/**
 * Thrown for exceptions encountered with database operations. Provides
 * information regarding which operations failed and the query (if any) it
 * failed on.
 */
class Exception extends RuntimeException
{
    /**
     * Creates a new dbunit operation exception
     */
    public function __construct(
        private readonly string $operation,
        private readonly string $query,
        private readonly array $args,
        private readonly ITable $table,
        private readonly string $error,
    ) {
        parent::__construct(sprintf('%s operation failed on query: %s using args: ', $operation, $query) . print_r($args, true) . sprintf(' [%s]', $error));
    }

    public function getOperation(): string
    {
        return $this->operation;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getTable(): ITable
    {
        return $this->table;
    }

    public function getArgs(): array
    {
        return $this->args;
    }

    public function getError(): string
    {
        return $this->error;
    }
}
