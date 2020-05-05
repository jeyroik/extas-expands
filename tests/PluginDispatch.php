<?php
namespace tests;

use extas\components\plugins\expands\PluginExpandAbstract;
use extas\interfaces\expands\IExpandingBox;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class PluginDispatch
 *
 * @package tests
 * @author jeyroik@gmail.com
 */
class PluginDispatch extends PluginExpandAbstract
{
    protected function dispatch(IExpandingBox &$parent, RequestInterface $request, ResponseInterface $response)
    {
        $parent->addToValue('status', 'Ok');
    }

    protected function getExpandName(): string
    {
        return 'status';
    }
}
