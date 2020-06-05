<?php
namespace extas\components\plugins\init;

use extas\components\expands\ExpandRequired;
use extas\interfaces\expands\IExpandRequiredRepository;

/**
 * Class InitExpandsRequired
 *
 * @package extas\components\init
 * @author jeyroik@gmail.com
 */
class InitExpandsRequired extends InitSection
{
    protected string $selfRepositoryClass = IExpandRequiredRepository::class;
    protected string $selfUID = ExpandRequired::FIELD__NAME;
    protected string $selfSection = 'required_expands';
    protected string $selfName = 'required expand';
    protected string $selfItemClass = ExpandRequired::class;
}
