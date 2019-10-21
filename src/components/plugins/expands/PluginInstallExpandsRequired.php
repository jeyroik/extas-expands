<?php
namespace extas\components\plugins;

use extas\components\expands\ExpandRequired;
use extas\interfaces\expands\IExpandRequiredRepository;

/**
 * Class PluginInstallExpandsRequired
 *
 * @package extas\components\plugins
 * @author jeyroik@gmail.com
 */
class PluginInstallExpandsRequired extends PluginInstallDefault
{
    protected $selfRepositoryClass = IExpandRequiredRepository::class;
    protected $selfUID = ExpandRequired::FIELD__NAME;
    protected $selfSection = 'required_expands';
    protected $selfName = 'required expand';
    protected $selfItemClass = ExpandRequired::class;
}
