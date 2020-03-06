<?php
namespace extas\components\expands;

use extas\components\repositories\Repository;
use extas\interfaces\expands\IExpandRequiredRepository;

/**
 * Class ExpandRequiredRepository
 *
 * @package extas\components\expands
 * @author jeyroik@gmail.com
 */
class ExpandRequiredRepository extends Repository implements IExpandRequiredRepository
{
    protected string $itemClass = ExpandRequired::class;
    protected string $name = 'expand_required';
    protected string $pk = ExpandRequired::FIELD__NAME;
    protected string $scope = 'extas';
    protected string $idAs = '';
}
