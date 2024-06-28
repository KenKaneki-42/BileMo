<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class IsCreatedAtBeforeUpdatedAt extends Constraint
{
    public $message = 'La date de création doit être antérieure ou égale à la date de mise à jour.';
}
