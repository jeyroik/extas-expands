<?php
namespace extas\components\plugins\expands;

use extas\components\access\AccessOperation;
use extas\components\players\Current;
use extas\components\plugins\Plugin;
use extas\interfaces\access\IAccess;
use extas\interfaces\expands\IExpandingBox;
use extas\interfaces\servers\requests\IServerRequest;
use extas\interfaces\servers\responses\IServerResponse;

/**
 * Class PluginExpandAbstract
 *
 * 1. Проверяет доступ текущего* пользователя на запрашиваемый функционал.
 * 2. Добавляет в expand родительского элемента имя текущего expand'a.
 * 3. Проверяет расширен ли запрос текущим expand'ом.
 * 3.1. Если да, то запускает диспетчиризацию данного экспанда, т.е. сбор информации для него.
 * 3.2. Если нет, обработка заканчивается.
 *
 * @package extas\components\plugins\expands
 * @author jeyroik@gmail.com
 */
abstract class PluginExpandAbstract extends Plugin
{
    /**
     * @var array
     */
    protected $access = [];

    /**
     * @param IExpandingBox $parent
     * @param IServerRequest $request
     * @param IServerResponse $response
     *
     * @return void
     */
    public function __invoke(&$parent, IServerRequest &$request, IServerResponse &$response)
    {
        if ($this->isAllowed()) {
            $expand = $this->getExpandName();
            $expand && $parent->addExpand($parent->getName() . '.' . $expand);

            if ($expand && $request->isExpandedWith($parent->getName() . '.' . $expand)) {
                $this->dispatch($parent, $request, $response);
            }
        }
    }

    /**
     * @return array
     */
    protected function getAccess()
    {
        $this->access[IAccess::FIELD__OBJECT] = $this->access[IAccess::FIELD__OBJECT]
            ?? Current::player()->getAliases();

        return $this->access;
    }

    /**
     * @return bool
     */
    protected function isAllowed()
    {
        return (new AccessOperation($this->getAccess()))->exists();
    }

    /**
     * @param $parent
     * @param IServerRequest $request
     * @param IServerResponse $response
     *
     * @return void
     */
    abstract protected function dispatch(IExpandingBox &$parent, IServerRequest &$request, IServerResponse &$response);

    /**
     * @return string
     */
    abstract protected function getExpandName(): string;
}
