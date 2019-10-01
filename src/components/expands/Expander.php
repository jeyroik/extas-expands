<?php
namespace extas\components\expands;

use extas\interfaces\expands\IExpander;
use extas\interfaces\expands\IExpandingBox;

/**
 * Class Expander
 *
 * @package extas\components\expands
 * @author jeyroik@gmail.com
 */
class Expander implements IExpander
{
    /**
     * @param string $root
     * @param string $boxName
     *
     * @return IExpandingBox
     */
    public static function getExpandingBox(string $root, string $boxName): IExpandingBox
    {
        return new ExpandingBox([
            IExpandingBox::FIELD__ROOT => $root,
            IExpandingBox::FIELD__NAME => $boxName,
            IExpandingBox::FIELD__VALUE => [],
            IExpandingBox::FIELD__EXPAND => [],
            IExpandingBox::DATA__MARKER . $boxName => null
        ]);
    }
}
