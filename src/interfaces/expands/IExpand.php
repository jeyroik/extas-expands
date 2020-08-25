<?php
namespace extas\interfaces\expands;

use extas\interfaces\http\IHasHttpIO;
use extas\interfaces\IItem;

/**
 * Interface IExpand
 *
 * @package extas\interfaces\expands
 * @author jeyroik@gmail.com
 */
interface IExpand extends IItem, IHasHttpIO
{
    public const SUBJECT = 'extas.expand';

    public const WILDCARD = '*';

    /**
     * @param IItem $subject
     * @return IItem
     */
    public function expand(IItem $subject): IItem;
}
