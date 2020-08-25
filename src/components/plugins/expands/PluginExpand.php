<?php
namespace extas\components\plugins\expands;

use extas\components\plugins\Plugin;
use extas\interfaces\expands\IBox;
use extas\interfaces\expands\IExpand;
use extas\interfaces\IItem;
use extas\interfaces\repositories\IRepository;
use extas\interfaces\stages\IStageExpand;

/**
 * Class PluginExpand
 *
 * @method IRepository expandBoxes()
 *
 * @package extas\components\plugins\expand
 * @author jeyroik <jeyroik@gmail.com>
 */
class PluginExpand extends Plugin implements IStageExpand
{
    public const FIELD__EXPAND = 'expand';

    /**
     * @param IItem $subject
     * @param IExpand $expand
     *
     * @return IItem
     */
    public function __invoke(IItem $subject, IExpand $expand): IItem
    {
        $subjectName = $subject->__subject();
        $boxes = $this->expandBoxes()->all([
            IBox::FIELD__ALIASES => [$subjectName, $expand::WILDCARD]
        ]);
        $subject[static::FIELD__EXPAND] = array_column($boxes, IBox::FIELD__NAME);

        return $subject;
    }
}
