<?php

declare(strict_types=1);

namespace Core\Service;

use Core\Exception\ApiValidationException;
use Core\Exception\VerboseExceptionInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class ValidatorService
{
    public function __construct(
        private ValidatorInterface $validator,
    ) {
    }

    /**
     * @throws VerboseExceptionInterface
     */
    public function validate(object $object, array $groups): void
    {
        $errors = [];

        $validationErrors = $this->validator->validate($object, null, $groups);

        if (\count($validationErrors) > 0) {
            /** @var ConstraintViolation $error */
            foreach ($validationErrors as $error) {
                // consider using serialized name to send instead of property path
                $errors[$error->getPropertyPath()] = $error->getMessage();
            }

            throw ApiValidationException::create('Validation failed', $errors);
        }
    }
}
