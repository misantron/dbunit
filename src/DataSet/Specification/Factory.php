<?php

/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\DbUnit\DataSet\Specification;

use PHPUnit\DbUnit\Exception\RuntimeException;

/**
 * Creates the appropriate DataSet Spec based on a given type.
 */
class Factory implements IFactory
{
    /**
     * Returns the data set
     *
     *
     */
    public function getDataSetSpecByType(string $type): Specification
    {
        return match ($type) {
            'xml' => new Xml(),
            'flatxml' => new FlatXml(),
            'csv' => new Csv(),
            'yaml' => new Yaml(),
            'dbtable' => new Table(),
            'dbquery' => new Query(),
            default => throw new RuntimeException("I don't know what you want from me."),
        };
    }
}
