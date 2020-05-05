<?php
namespace extas\interfaces\stages;

use extas\interfaces\errors\IHasErrors;
use extas\interfaces\expands\IExpandingBox;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface IStageExpandBox
 *
 * @package extas\interfaces\stages
 * @author jeyroik@gmail.com
 */
interface IStageExpandBox extends IHasErrors
{
    public const STAGE__PREFIX = 'expand.';

    /**
     * @param IExpandingBox $box
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    public function __invoke(IExpandingBox &$box, RequestInterface $request, ResponseInterface $response): void;
}
