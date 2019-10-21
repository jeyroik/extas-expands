<?php
namespace extas\interfaces\expands;

use extas\interfaces\IHasName;
use extas\interfaces\IItem;

/**
 * Interface IExpandRequired
 *
 * @package extas\interfaces\expands
 * @author jeyroik@gmail.com
 */
interface IExpandRequired extends IItem, IHasName
{
    const SUBJECT = 'extas.expand.required';

    const FIELD__ROUTES = 'routes';
    const FIELD__ACCEPT = 'accept';

    /**
     * @return array
     */
    public function getRoutes(): array;

    /**
     * @return array
     */
    public function getAccept(): array;

    /**
     * @param array $routes
     *
     * @return IExpandRequired
     */
    public function setRoutes(array $routes): IExpandRequired;

    /**
     * @param array $accept
     *
     * @return IExpandRequired
     */
    public function setAccept(array $accept): IExpandRequired;
}
