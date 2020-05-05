<?php
namespace tests;

use extas\components\plugins\Plugin;
use extas\interfaces\expands\IExpandingBox;
use extas\interfaces\stages\IStageExpandBox;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class PluginRoot
 *
 * @package tests
 * @author jeyroik@gmail.com
 */
class PluginRoot extends Plugin implements IStageExpandBox
{
    /**
     * @param IExpandingBox $box
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    public function __invoke(IExpandingBox &$box, RequestInterface $request, ResponseInterface &$response): void
    {
        $box->addToValue('test.root.box', 'is ok');
    }
}
