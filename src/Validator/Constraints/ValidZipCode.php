<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ValidZipCode extends Constraint
{
    public string $message = 'Le code postal "{{ value }}" n\'existe pas en France.';
}