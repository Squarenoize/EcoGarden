<?php
namespace App\Validator\Constraints;

use App\Service\ExternalApiService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ValidInseeValidator extends ConstraintValidator
{
    public function __construct(
        private ExternalApiService $externalApiService
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
            $response = $this->externalApiService->getInsee((int) $value);

            $data = $response->toArray(false);
            $isInvalid = $response->getStatusCode() >= 400
                || 0 === (int) ($data['numberMatched'] ?? 0);

            if ($isInvalid) {
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