<?php
namespace extas\components\expands;

use extas\components\samples\parameters\SampleParameter;
use extas\components\THasDescription;
use extas\interfaces\errors\IHasErrors;
use extas\interfaces\expands\IExpandingBox;
use extas\interfaces\stages\IStageExpandBox;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ExpandingBox
 *
 * @package extas\components\expands
 * @author jeyroik@gmail.com
 */
class ExpandingBox extends SampleParameter implements IExpandingBox
{
    use THasDescription;

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    public function expand(RequestInterface $request, ResponseInterface $response)
    {
        $this->runPluginsByStage(IStageExpandBox::STAGE__PREFIX . $this->getName(), $request, $response);

        $root = $this->getRoot();

        $this->runPluginsByStage(
            IStageExpandBox::STAGE__PREFIX . $root . '.' . $this->getName(),
            $request,
            $response
        );
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

    /**
     * @return bool
     */
    public function isPacked(): bool
    {
        return $this->config[static::FIELD__IS_PACKED] ?? false;
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
        $this->config[static::FIELD__IS_PACKED] = true;

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
        $this->config[static::FIELD__IS_PACKED] = false;

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

    /**
     * @param string $stage
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    protected function runPluginsByStage(string $stage, RequestInterface $request, ResponseInterface $response)
    {
        foreach ($this->getPluginsByStage($stage) as $plugin) {
            /**
             * @var IStageExpandBox|IHasErrors $plugin
             */
            $plugin($this, $request, $response);

            if ($plugin->hasErrors()) {
                $value = $this->getValue();
                $errors = $value['errors'] ?? [];
                $errors = array_merge($errors, $plugin->getErrors());
                $this->addToValue('errors', $errors);
            }
        }
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return 'extas.expand.box';
    }
}
