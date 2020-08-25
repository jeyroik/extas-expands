<?php
namespace extas\components\expands;

use extas\components\http\THasHttpIO;
use extas\components\Item;
use extas\interfaces\expands\IExpand;
use extas\interfaces\IItem;
use extas\interfaces\stages\IStageExpand;
use extas\interfaces\stages\IStageExpandParse;


/**
 * Class Expand
 *
 * @package extas\components\expands
 * @author jeyroik@gmail.com
 */
class Expand extends Item implements IExpand
{
    use THasHttpIO;

    public const ARG__EXPAND = 'expand';
    public const EXPAND__DEFAULT = '@expand';

    /**
     * @param IItem $subject
     * @return IItem
     */
    public function expand(IItem $subject): IItem
    {
        $expands = $this->parseExpand();

        foreach ($expands as $expand) {
            $subject = $this->expandBy($expand, $subject);
        }

        return $subject;
    }

    /**
     * @param string $expand
     * @param IItem $subject
     * @return IItem
     */
    protected function expandBy(string $expand, IItem $subject): IItem
    {
        foreach ($this->getPluginsByStage(IStageExpand::NAME . '.' . $expand) as $plugin) {
            /**
             * @var IStageExpand $plugin
             */
            $subject = $plugin($subject, $this);
        }

        return $subject;
    }

    /**
     * @return array
     */
    protected function parseExpand(): array
    {
        $args = $this->getArguments();
        $expand = $args[static::ARG__EXPAND] ?? '';
        $expands = explode(',', $expand);
        $expands[] = static::EXPAND__DEFAULT;

        foreach ($expands as $index => $expand) {
            $expands[$index] = trim(strtolower($expand));
        }

        foreach ($this->getPluginsByStage(IStageExpandParse::NAME) as $plugin) {
            /**
             * @var IStageExpandParse $plugin
             */
            $expands = $plugin($expands);
        }

        natsort($expands);

        return $expands;
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return static::SUBJECT;
    }
}
