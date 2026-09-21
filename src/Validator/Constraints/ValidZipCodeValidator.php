<?php
namespace App\Validator\Constraints;

use App\Service\ExternalApiService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ValidZipCodeValidator extends ConstraintValidator
{
    public function __construct(
        private ExternalApiService $externalApiService
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
            $response = $this->externalApiService->getZipCode((int) $value);
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