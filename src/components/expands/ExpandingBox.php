<?php
namespace extas\components\expands;

use extas\components\parameters\Parameter;
use extas\components\THasDescription;
use extas\interfaces\expands\IExpandingBox;
use extas\interfaces\servers\requests\IServerRequest;
use extas\interfaces\servers\responses\IServerResponse;

/**
 * Class ExpandingBox
 *
 * @package extas\components\expands
 * @author jeyroik@gmail.com
 */
class ExpandingBox extends Parameter implements IExpandingBox
{
    use THasDescription;

    /**
     * @param IServerRequest $request
     * @param IServerResponse $response
     */
    public function expand(IServerRequest $request, IServerResponse $response)
    {
        foreach ($this->getPluginsByStage('expand.' . $this->getName()) as $plugin) {
            $plugin($this, $request, $response);
        }

        $root = $this->getRoot();

        foreach ($this->getPluginsByStage('expand.' . $root . '.' . $this->getName()) as $plugin) {
            $plugin($this, $request, $response);
        }
    }

    /**
     * @return string
     */
    public function getRoot(): string
    {
        return $this->config[static::FIELD__ROOT] ?? '';
    }

    /**
     * @param string $root
     *
     * @return IExpandingBox
     */
    public function setRoot(string $root): IExpandingBox
    {
        $this->config[static::FIELD__ROOT] = $root;

        return $this;
    }

    public function isPacked(): bool
    {
        // TODO: Implement isPacked() method.
    }

    /**
     * @param string $key
     * @param mixed $data
     *
     * @return $this
     */
    public function addToValue($key, $data): IExpandingBox
    {
        // Убеждаемся, что в FIELD__VALUE есть массив.
        $this->config[static::FIELD__VALUE] = $this->getValue([]);
        $this->config[static::FIELD__VALUE][$key] = $data;

        return $this;
    }

    /**
     * @return array
     */
    public function getExpand(): array
    {
        return $this->config[static::FIELD__EXPAND] ?? [];
    }

    /**
     * @param array $expand
     *
     * @return $this
     */
    public function setExpand(array $expand): IExpandingBox
    {
        $this->config[static::FIELD__EXPAND] = $expand;

        return $this;
    }

    /**
     * @param string $expanding
     *
     * @return $this
     */
    public function addExpand(string $expanding): IExpandingBox
    {
        $expand = $this->getExpand();
        $expand[] = $expanding;
        $this->setExpand($expand);

        return $this;
    }

    /**
     * @return $this
     */
    public function pack(): IExpandingBox
    {
        $this->addToValue(static::FIELD__EXPAND, $this->getExpand());

        return $this;
    }

    /**
     * @return $this
     */
    public function unpack(): IExpandingBox
    {
        $value = $this->getValue([]);
        if (isset($value[static::FIELD__EXPAND])) {
            unset($value[static::FIELD__EXPAND]);
        }
        $this->setValue($value);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->config[static::DATA__MARKER . $this->getName()] ?? null;
    }

    /**
     * @param mixed $data
     *
     * @return $this
     */
    public function setData($data): IExpandingBox
    {
        $this->config[static::DATA__MARKER . $this->getName()] = $data;

        return $this;
    }
}
