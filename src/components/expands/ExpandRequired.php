<?php
namespace extas\components\expands;

use extas\components\Item;
use extas\components\THasName;
use extas\interfaces\expands\IExpandRequired;

/**
 * Class ExpandRequired
 *
 * @package extas\components\expands
 * @author jeyroik@gmail.com
 */
class ExpandRequired extends Item implements IExpandRequired
{
    use THasName;

    /**
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->config[static::FIELD__ROUTES] ?? [];
    }

    /**
     * @return array
     */
    public function getAccept(): array
    {
        return $this->config[static::FIELD__ACCEPT] ?? [];
    }

    /**
     * @param array $routes
     *
     * @return IExpandRequired
     */
    public function setRoutes(array $routes): IExpandRequired
    {
        $this->config[static::FIELD__ROUTES] = $routes;

        return $this;
    }

    /**
     * @param array $accept
     *
     * @return IExpandRequired
     */
    public function setAccept(array $accept): IExpandRequired
    {
        $this->config[static::FIELD__ACCEPT] = $accept;

        return $this;
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return static::SUBJECT;
    }
}
