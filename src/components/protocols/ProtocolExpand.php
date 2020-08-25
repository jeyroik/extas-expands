<?php
namespace extas\components\protocols;

use extas\components\expands\Expand;
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
class ProtocolExpand extends ProtocolParameterHeaderDefault
{
    protected string $protocolKey = Expand::ARG__EXPAND;

    /**
     * @param array $args
     * @param RequestInterface|null $request
     */
    public function __invoke(array &$args = [], RequestInterface $request = null): void
    {
        parent::__invoke($args, $request);

        if (isset($args[Expand::ARG__EXPAND])) {
            $args[Expand::ARG__EXPAND] = $this->normalizeExpand($args[Expand::ARG__EXPAND]);
        }
    }

    /**
     * @param string $expandString
     * @return array
     */
    protected function normalizeExpand(string $expandString): array
    {
        $expands = explode(',', $expandString);

        foreach ($expands as $index => $expand) {
            $expands[$index] = trim(strtolower($expand));
        }

        return $expands;
    }
}
