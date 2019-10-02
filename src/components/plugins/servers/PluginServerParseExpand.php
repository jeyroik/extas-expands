<?php
namespace extas\components\plugins\servers;

use extas\components\plugins\Plugin;
use extas\interfaces\expands\IExpandingBox;
use extas\interfaces\servers\requests\IServerRequestParser;
use Psr\Http\Message\RequestInterface;

/**
 * Class PluginServerParseExpand
 *
 * @stage extas.server.parser.parse
 * @package extas\components\plugins\servers
 * @author jeyroik@gmail.com
 */
class PluginServerParseExpand extends Plugin
{
    const FIELD__HEADER_EXPAND = 'header_expand';
    const DEFAULT__HEADER_EXPAND = 'x-extas-expand';

    /**
     * @param IServerRequestParser $parser
     * @param RequestInterface $request
     */
    public function __invoke(IServerRequestParser &$parser, RequestInterface $request)
    {
        $headerName = isset($parser[static::FIELD__HEADER_EXPAND])
            ? $parser[static::FIELD__HEADER_EXPAND]
            : static::DEFAULT__HEADER_EXPAND;

        $headers = $request->getHeader($headerName);
        if (count($headers)) {
            $parser[IExpandingBox::FIELD__EXPAND] = array_shift($headers);
        }
    }
}
