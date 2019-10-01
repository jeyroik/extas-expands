<?php
namespace extas\interfaces\expands;

/**
 * Interface IExpander
 *
 * @package extas\interfaces\expands
 * @author jeyroik@gmail.com
 */
interface IExpander
{
    /**
     * @param string $root
     * @param string $boxName
     *
     * @return IExpandingBox
     */
    public static function getExpandingBox(string $root, string $boxName): IExpandingBox;
}
