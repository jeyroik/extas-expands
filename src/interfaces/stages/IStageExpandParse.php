<?php
namespace extas\interfaces\stages;

/**
 * Interface IStageExpandParse
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageExpandParse
{
    public const NAME = 'extas.expand.parse';

    /**
     * @param array $expands
     * @return array
     */
    public function __invoke(array $expands): array;
}
