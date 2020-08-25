<?php
namespace extas\interfaces\expands;

use extas\interfaces\IHasAliases;
use extas\interfaces\IHasDescription;
use extas\interfaces\IHasName;
use extas\interfaces\IItem;

/**
 * Interface IBox
 *
 * @package extas\interfaces\expands
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IBox extends IItem, IHasName, IHasAliases, IHasDescription
{
    public const SUBJECT = 'extas.expand.box';
}
