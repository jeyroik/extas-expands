<?php
namespace extas\components\plugins\init;

use extas\components\expands\ExpandRequired;

/**
 * Class InitExpandsRequired
 *
 * @package extas\components\init
 * @author jeyroik@gmail.com
 */
class InitExpandsRequired extends InitSection
{
    protected string $selfRepositoryClass = 'expandRequiredRepository';
    protected string $selfUID = ExpandRequired::FIELD__NAME;
    protected string $selfSection = 'required_expands';
    protected string $selfName = 'required expand';
    protected string $selfItemClass = ExpandRequired::class;
}
