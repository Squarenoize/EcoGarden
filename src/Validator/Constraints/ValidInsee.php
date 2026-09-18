<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ValidInsee extends Constraint
{
    public string $message = 'Le code INSEE "{{ value }}" n\'existe pas en France.';
}