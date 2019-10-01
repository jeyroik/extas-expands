<?php
namespace extas\interfaces\expands;

use extas\interfaces\IHasDescription;
use extas\interfaces\parameters\IParameter;
use extas\interfaces\servers\requests\IServerRequest;
use extas\interfaces\servers\responses\IServerResponse;

/**
 * Interface IExpandingBox
 *
 * @package extas\interfaces\expands
 * @author jeyroik@gmail.com
 */
interface IExpandingBox extends IParameter, IHasDescription
{
    const SUBJECT = 'extas.expand.box';
    const DATA__MARKER = '$';
    const FIELD__ROOT = 'root';
    const FIELD__EXPAND = 'expand';
    const FIELD__IS_PACKED = 'is_packed';

    public function getRoot(): string;

    /**
     * @param string $root
     *
     * @return $this
     */
    public function setRoot(string $root): IExpandingBox;

    /**
     * @return bool
     */
    public function isPacked(): bool;

    /**
     * @param IServerRequest $request
     * @param IServerResponse $response
     *
     * @return void
     */
    public function expand(IServerRequest $request, IServerResponse $response);

    /**
     * @param string $key
     * @param mixed $data
     *
     * @return $this
     */
    public function addToValue($key, $data): IExpandingBox;

    /**
     * @return array
     */
    public function getExpand(): array;

    /**
     * @param array $expand
     *
     * @return $this
     */
    public function setExpand(array $expand): IExpandingBox;

    /**
     * @param string $expanding
     *
     * @return $this
     */
    public function addExpand(string $expanding): IExpandingBox;

    /**
     * @return mixed
     */
    public function getData();

    /**
     * @param mixed $data
     *
     * @return $this
     */
    public function setData($data): IExpandingBox;

    /**
     * @return $this
     */
    public function pack(): IExpandingBox;

    /**
     * @return $this
     */
    public function unpack(): IExpandingBox;
}
