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

/**
 * A class factory to easily return database operations.
 */
class Factory
{
    /**
     * Returns a null database operation
     *
     * @return Operation
     */
    public static function NONE(): None
    {
        return new None();
    }

    /**
     * Returns a clean insert database operation. It will remove all contents
     * from the table prior to re-inserting rows.
     *
     * @param bool $cascadeTruncates set to true to force truncates to cascade on databases that support this
     *
     * @return Operation
     */
    public static function CLEAN_INSERT(bool $cascadeTruncates = false): Composite
    {
        return new Composite([
            self::TRUNCATE($cascadeTruncates),
            self::INSERT(),
        ]);
    }

    /**
     * Returns an insert database operation.
     *
     * @return Operation
     */
    public static function INSERT(): Insert
    {
        return new Insert();
    }

    /**
     * Returns a truncate database operation.
     *
     * @param bool $cascadeTruncates set to true to force truncates to cascade on databases that support this
     *
     * @return Operation
     */
    public static function TRUNCATE(bool $cascadeTruncates = false): Truncate
    {
        $truncate = new Truncate();
        $truncate->setCascade($cascadeTruncates);

        return $truncate;
    }

    /**
     * Returns a delete database operation.
     *
     * @return Operation
     */
    public static function DELETE(): Delete
    {
        return new Delete();
    }

    /**
     * Returns a delete_all database operation.
     *
     * @return Operation
     */
    public static function DELETE_ALL(): DeleteAll
    {
        return new DeleteAll();
    }

    /**
     * Returns an update database operation.
     *
     * @return Operation
     */
    public static function UPDATE(): Update
    {
        return new Update();
    }
}
