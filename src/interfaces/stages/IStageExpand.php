<?php
namespace extas\interfaces\stages;

use extas\interfaces\expands\IExpand;
use extas\interfaces\IItem;

/**
 * Interface IStageExpandBox
 *
 * @package extas\interfaces\stages
 * @author jeyroik@gmail.com
 */
interface IStageExpand
{
    public const NAME = 'extas.expand';

    /**
     * @param IItem $subject
     * @param IExpand $expand
     * @return IItem
     */
    public function __invoke(IItem $subject, IExpand $expand): IItem;
}
