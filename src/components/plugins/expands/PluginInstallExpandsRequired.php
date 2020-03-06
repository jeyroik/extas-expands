<?php
namespace extas\components\plugins\expands;

use extas\components\expands\ExpandRequired;
use extas\components\plugins\PluginInstallDefault;
use extas\interfaces\expands\IExpandRequiredRepository;

/**
 * Class PluginInstallExpandsRequired
 *
 * @package extas\components\plugins
 * @author jeyroik@gmail.com
 */
class PluginInstallExpandsRequired extends PluginInstallDefault
{
    protected string $selfRepositoryClass = IExpandRequiredRepository::class;
    protected string $selfUID = ExpandRequired::FIELD__NAME;
    protected string $selfSection = 'required_expands';
    protected string $selfName = 'required expand';
    protected string $selfItemClass = ExpandRequired::class;
}
