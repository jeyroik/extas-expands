<?php
namespace tests\expands\misc;

use extas\components\plugins\Plugin;
use extas\interfaces\expands\IExpand;
use extas\interfaces\IItem;
use extas\interfaces\stages\IStageExpand;

/**
 * Class ExpandTestIsOk
 *
 * @package tests\expands\misc
 * @author jeyroik <jeyroik@gmail.com>
 */
class ExpandTestIsOk extends Plugin implements IStageExpand
{
    /**
     * @param IItem $subject
     * @param IExpand $expand
     * @return IItem
     */
    public function __invoke(IItem $subject, IExpand $expand): IItem
    {
        $subject['test'] = 'is ok';

        return $subject;
    }
}
