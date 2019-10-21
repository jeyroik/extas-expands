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
    protected $itemClass = ExpandRequired::class;
    protected $name = 'expand_required';
    protected $pk = ExpandRequired::FIELD__NAME;
    protected $scope = 'extas';
    protected $idAs = '';
}
