<?php
namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ValidZipCodeValidator extends ConstraintValidator
{
    public function __construct(
        private HttpClientInterface $httpClient
    ) {}

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidZipCode) {
            throw new UnexpectedTypeException($constraint, ValidZipCode::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        try {
            $response = $this->httpClient->request(
                'GET', 
                "https://apicarto.ign.fr/api/codes-postaux/communes/{$value}",
            );
            

            $data = $response->toArray();
            
            if (empty($data)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', (string) $value)
                    ->addViolation();
            }
        } catch (\Exception $e) {
            $this->context->buildViolation('Impossible de vérifier le code postal.')
                ->addViolation();
        }
    }
}