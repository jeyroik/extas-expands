<?php
namespace tests\expands\misc;

use extas\components\plugins\expands\PluginExpandAbstract;
use extas\interfaces\expands\IExpandingBox;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class PluginException
 *
 * @package tests\expands\misc
 * @author jeyroik@gmail.com
 */
class PluginException extends PluginExpandAbstract
{
    /**
     * @param IExpandingBox $parent
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @throws \Exception
     */
    protected function dispatch(IExpandingBox &$parent, RequestInterface $request, ResponseInterface $response)
    {
        throw new \Exception('Unexpected');
    }

    /**
     * @return string
     */
    protected function getExpandName(): string
    {
        return 'status';
    }
}
