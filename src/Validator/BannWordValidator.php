<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class BannWordValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /** @var BannWord $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        $value = strtolower($value);
        foreach ($constraint->bannWords as $bannword) {
            if (str_contains($value, $bannword)) {
                $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $bannword)
                ->addViolation();
            }
        }
        
    }
}
