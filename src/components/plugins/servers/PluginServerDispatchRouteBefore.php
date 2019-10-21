<?php
namespace extas\components\plugins\servers;

use extas\components\plugins\Plugin;
use extas\components\SystemContainer;
use extas\interfaces\expands\IExpandRequired;
use extas\interfaces\expands\IExpandRequiredRepository;
use extas\interfaces\servers\requests\IServerRequest;
use extas\interfaces\servers\responses\IServerResponse;

/**
 * Class PluginServerDispatchRouteBefore
 *
 * @stage route.before
 * @package extas\components\plugins\servers
 * @author jeyroik@gmail.com
 */
class PluginServerDispatchRouteBefore extends Plugin
{
    /**
     * @param IServerRequest $serverRequest
     * @param IServerResponse $serverResponse
     * @param $section
     * @param $subject
     * @param $operation
     */
    public function __invoke(IServerRequest &$serverRequest, IServerResponse $serverResponse, $section, $subject, $operation)
    {
        $routesForSearch = [
            '*',
            $section . '.*',
            $section . '.' . $subject . '.*',
            $section . '.' . $subject . '.' . $operation
        ];

        /**
         * @var $repo IExpandRequiredRepository
         * @var $requiredExpands IExpandRequired[]
         */
        $repo = SystemContainer::getItem(IExpandRequiredRepository::class);
        $requiredExpands = $repo->all([
            IExpandRequired::FIELD__ROUTES => $routesForSearch,
            IExpandRequired::FIELD__ACCEPT => $serverRequest->getParameter('accept')->getValue('')
        ]);
        $expand = $serverRequest->getParameter('expand');
        foreach ($requiredExpands as $requiredExpand) {
            $expand->setValue($expand->getValue() . ',' . $requiredExpand->getName());
        }
        $serverRequest->setParameter('expand', $expand);
    }
}
