<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use App\Entity\TimestampableEntityInterface;

class IsCreatedAtBeforeUpdatedAtValidator extends ConstraintValidator
{
    public function validate($objectToValidate, Constraint $constraint)
    {
        if (!$constraint instanceof IsCreatedAtBeforeUpdatedAt) {
            throw new UnexpectedTypeException($constraint, IsCreatedAtBeforeUpdatedAt::class);
        }

        if (!$objectToValidate instanceof TimestampableEntityInterface) {
          throw new UnexpectedValueException($objectToValidate, TimestampableEntityInterface::class);
      }

        if ($objectToValidate->getCreatedAt() > $objectToValidate->getUpdatedAt()) {
            $this->context->buildViolation($constraint->message)
                ->atPath('updatedAt')
                ->addViolation();
        }
    }
}
