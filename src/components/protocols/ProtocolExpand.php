<?php
namespace extas\components\protocols;

use extas\interfaces\expands\IExpandingBox;
use Psr\Http\Message\RequestInterface;

/**
 * Class ProtocolExpand
 *
 * Парсит параметры и заголовки запроса на предмет наличия expand'a.
 * Дефолтное значение можно переопределить через переменную окружения EXTAS__PROTOCOL_EXPAND__DEFAULT.
 * Префикс заголовка можно переопределить через переменную окружения EXTAS__PROTOCOL_EXPAND__HEADER_PREFIX.
 *
 * @package extas\components\protocols
 * @author jeyroik@gmail.com
 */
class ProtocolExpand extends Protocol
{
    const HEADER__PREFIX = 'x-extas-';

    /**
     * @param array $args
     * @param RequestInterface $request
     */
    public function __invoke(array &$args = [], RequestInterface $request)
    {
        $fromHeader = $this->grabHeaders($request);
        $fromParameter = $this->grabParameters($request);
        $default = $this->getDefault();

        if (!isset($args[IExpandingBox::FIELD__EXPAND])) {
            $args[IExpandingBox::FIELD__EXPAND] = is_null($fromParameter)
                ? (is_null($fromHeader)
                    ? $default
                    : $fromHeader)
                : $fromParameter;
        }
    }

    /**
     * @return string
     */
    protected function getDefault(): string
    {
        return getenv('EXTAS__PROTOCOL_EXPAND__DEFAULT') ?: '';
    }

    /**
     * @param RequestInterface $request
     *
     * @return null|string
     */
    protected function grabHeaders(RequestInterface $request): ?string
    {
        $headerPrefix = getenv('EXTAS__PROTOCOL_EXPAND__HEADER_PREFIX') ?: static::HEADER__PREFIX;

        $headerName = $headerPrefix . IExpandingBox::FIELD__EXPAND;
        $headers = $request->getHeader($headerName);
        if (count($headers)) {
            return array_shift($headers);
        }

        return null;
    }

    /**
     * @param RequestInterface $request
     *
     * @return null|string
     */
    protected function grabParameters(RequestInterface $request): ?string
    {
        parse_str($request->getUri()->getQuery(), $queryParams);

        return $queryParams[IExpandingBox::FIELD__EXPAND] ?? null;
    }
}
