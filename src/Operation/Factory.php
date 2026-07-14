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
     */
    public static function NONE(): Operation
    {
        return new None();
    }

    /**
     * Returns a clean insert database operation. It will remove all contents
     * from the table prior to re-inserting rows.
     *
     * @param bool $cascadeTruncates set to true to force truncates to cascade on databases that support this
     */
    public static function CLEAN_INSERT(bool $cascadeTruncates = false): Operation
    {
        return new Composite([
            self::TRUNCATE($cascadeTruncates),
            self::INSERT(),
        ]);
    }

    /**
     * Returns an insert database operation.
     */
    public static function INSERT(): Operation
    {
        return new Insert();
    }

    /**
     * Returns a truncate database operation.
     *
     * @param bool $cascadeTruncates set to true to force truncates to cascade on databases that support this
     */
    public static function TRUNCATE(bool $cascadeTruncates = false): Operation
    {
        $truncate = new Truncate();
        $truncate->setCascade($cascadeTruncates);

        return $truncate;
    }

    /**
     * Returns a delete database operation.
     */
    public static function DELETE(): Operation
    {
        return new Delete();
    }

    /**
     * Returns a delete_all database operation.
     */
    public static function DELETE_ALL(): Operation
    {
        return new DeleteAll();
    }

    /**
     * Returns an update database operation.
     */
    public static function UPDATE(): Operation
    {
        return new Update();
    }
}
