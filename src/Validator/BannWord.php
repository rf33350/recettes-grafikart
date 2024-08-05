<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class BannWord extends Constraint
{
    public function __construct(
        public string $message = 'Un mot contenant "{{ value }}" n\'est pas valide.',
        public array $bannWords= ['spam', 'viagra'],
        ?array $groups = null,
        mixed $payload = null)
    {
        parent::__construct(null, $groups, $payload);
    }  
    
}
