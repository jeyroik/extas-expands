<?php
namespace extas\components\plugins\expands;

use extas\components\errors\Error;
use extas\components\errors\THasErrors;
use extas\components\plugins\Plugin;
use extas\components\protocols\ProtocolExpand;
use extas\interfaces\expands\IExpandingBox;
use extas\interfaces\stages\IStageExpandBox;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class PluginExpandAbstract
 *
 * - Добавляет в expand родительского элемента имя текущего expand'a.
 * - Проверяет расширен ли запрос текущим expand'ом.
 * -- Если да, то запускает диспетчиризацию данного экспанда, т.е. сбор информации для него.
 * -- Если нет, обработка заканчивается.
 *
 * @package extas\components\plugins\expands
 * @author jeyroik@gmail.com
 */
abstract class PluginExpandAbstract extends Plugin implements IStageExpandBox
{
    use THasErrors;

    /**
     * @param IExpandingBox $parent
     * @param RequestInterface $request
     * @param ResponseInterface $response
     *
     * @return void
     */
    public function __invoke(IExpandingBox &$parent, RequestInterface $request, ResponseInterface $response): void
    {
        $expand = $this->getExpandName();
        $expand && $parent->addExpand($parent->getName() . '.' . $expand);

        if ($expand && $this->isRequestExpandedWith($request, $parent->getName() . '.' . $expand)) {
            try {
                $this->dispatch($parent, $request, $response);
            } catch (\Exception $e) {
                $this->addError(new Error([
                    Error::FIELD__NAME => get_class($e),
                    Error::FIELD__TITLE => 'Expanding error',
                    Error::FIELD__DESCRIPTION => $e->getMessage(),
                    Error::FIELD__CODE => $e->getCode()
                ]));
            }
        }
    }

    /**
     * @param RequestInterface $request
     * @param string $expandName
     * @return bool
     */
    protected function isRequestExpandedWith(RequestInterface $request, string $expandName): bool
    {
        $protocol = new ProtocolExpand();
        $args = [];
        $protocol($args, $request);
        return !empty($args[IExpandingBox::FIELD__EXPAND]) && ($args[IExpandingBox::FIELD__EXPAND] == $expandName);
    }

    /**
     * @param $parent
     * @param RequestInterface $request
     * @param ResponseInterface $response
     *
     * @return void
     */
    abstract protected function dispatch(
        IExpandingBox &$parent,
        RequestInterface $request,
        ResponseInterface $response
    );

    /**
     * @return string
     */
    abstract protected function getExpandName(): string;
}
