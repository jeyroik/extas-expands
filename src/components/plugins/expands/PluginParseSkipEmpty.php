<?php
namespace extas\components\plugins\expands;

use extas\components\plugins\Plugin;
use extas\interfaces\stages\IStageExpandParse;

/**
 * Class PluginParseSkipEmpty
 *
 * @package extas\components\plugins\expands
 * @author jeyroik <jeyroik@gmail.com>
 */
class PluginParseSkipEmpty extends Plugin implements IStageExpandParse
{
    /**
     * @param array $expands
     * @return array
     */
    public function __invoke(array $expands): array
    {
        foreach ($expands as $index => $expand) {
            $this->removeEmpty($expand, $index, $expands);
        }

        return $expands;
    }

    /**
     * @param string $expand
     * @param array $new
     */
    protected function removeEmpty(string $expand, int $index, array &$expands): void
    {
        if (empty($expand)) {
            unset($expands[$index]);
        }
    }
}
