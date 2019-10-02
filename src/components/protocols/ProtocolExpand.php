<?php
namespace extas\components\protocols;

use extas\interfaces\expands\IExpandingBox;

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
    protected $protocolKey = IExpandingBox::FIELD__EXPAND;
}
