<?php
namespace tests\expands\misc;

use extas\components\errors\THasErrors;
use extas\components\plugins\Plugin;
use extas\interfaces\expands\IExpandingBox;
use extas\interfaces\stages\IStageExpandBox;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class PluginBox
 *
 * @package tests\exapnds\misc
 * @author jeyroik@gmail.com
 */
class PluginBox extends Plugin implements IStageExpandBox
{
    use THasErrors;

    /**
     * @param IExpandingBox $box
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    public function __invoke(IExpandingBox &$box, RequestInterface $request, ResponseInterface $response): void
    {
        $box->addToValue('test.box', 'is ok');
    }
}
