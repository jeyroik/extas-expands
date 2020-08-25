<?php
namespace extas\components\plugins\expands;

use extas\components\plugins\Plugin;
use extas\interfaces\expands\IBox;
use extas\interfaces\expands\IExpand;
use extas\interfaces\repositories\IRepository;
use extas\interfaces\stages\IStageExpandParse;

/**
 * Class PluginParseWildcard
 *
 * @method IRepository expandBoxes()
 *
 * @package extas\components\plugins\expands
 * @author jeyroik <jeyroik@gmail.com>
 */
class PluginParseWildcard extends Plugin implements IStageExpandParse
{
    /**
     * @param array $expands
     * @return array
     */
    public function __invoke(array $expands): array
    {
        $new = [];

        foreach ($expands as $index => $expand) {
            $this->unpackByWildcard($expand, $new);
        }

        return $new;
    }

    /**
     * @param string $expand
     * @param array $new
     */
    protected function unpackByWildcard(string $expand, array &$new): void
    {
        if (strpos($expand, IExpand::WILDCARD) === false) {
            $this->appendSingleExpand($expand, $new);
        } else {
            list($alias) = explode('.' . IExpand::WILDCARD, $expand);
            $boxes = $this->expandBoxes()->all([
                IBox::FIELD__ALIASES => [$alias, IExpand::WILDCARD]
            ]);

            $new = array_merge($new, array_diff(array_column($boxes, IBox::FIELD__NAME), $new));
        }
    }

    /**
     * @param string $expand
     * @param array $new
     */
    protected function appendSingleExpand(string $expand, array &$new): void
    {
        if (!in_array($expand, $new)) {
            $new[] = $expand;
        }
    }
}
