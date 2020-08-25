<?php
namespace extas\components\expands;

use extas\components\Item;
use extas\components\THasAliases;
use extas\components\THasDescription;
use extas\components\THasName;
use extas\interfaces\expands\IBox;

/**
 * Class Box
 *
 * @package extas\components\expands
 * @author jeyroik <jeyroik@gmail.com>
 */
class Box extends Item implements IBox
{
    use THasName;
    use THasDescription;
    use THasAliases;

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return static::SUBJECT;
    }
}
