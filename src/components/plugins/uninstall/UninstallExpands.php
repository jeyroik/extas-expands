<?php
namespace extas\components\plugins\uninstall;

use extas\components\expands\ExpandRequired;

/**
 * Class UninstallExpands
 *
 * @package extas\components\uninstall
 * @author jeyroik@gmail.com
 */
class UninstallExpands extends UninstallSection
{
    protected string $selfRepositoryClass = 'expandRequiredRepository';
    protected string $selfUID = ExpandRequired::FIELD__NAME;
    protected string $selfSection = 'required_expands';
    protected string $selfName = 'required expand';
    protected string $selfItemClass = ExpandRequired::class;
}
