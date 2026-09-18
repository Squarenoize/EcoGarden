<?php
namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ValidInseeValidator extends ConstraintValidator
{
    public function __construct(
        private HttpClientInterface $httpClient
    ) {}

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidInsee) {
            throw new UnexpectedTypeException($constraint, ValidInsee::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        try {
            $response = $this->httpClient->request(
                'GET', 
                "https://apicarto.ign.fr/api/cadastre/commune?code_insee={$value}",
            );
            
            // If code 400 response, it means the INSEE code is invalid
            if (400 === $response->getStatusCode()) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', (string) $value)
                    ->addViolation();
            }
        } catch (\Exception $e) {
            $this->context->buildViolation('Impossible de vérifier le code INSEE.')
                ->addViolation();
        }
    }
}