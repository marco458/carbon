<?php

declare(strict_types=1);

namespace Core\State\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Core\Entity\User\User;
use Core\Exception\ApiInvalidResourceException;
use Core\Exception\ApiValidationException;
use Core\Repository\UserRepository;
use Doctrine\DBAL\Driver\Exception;

final readonly class UserDeleteStateProcessor implements ProcessorInterface
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): string|null
    {
        if (!$data instanceof User) {
            // let's tell phpstan that this is a User object
            throw new ApiInvalidResourceException('Expecting user for deletion');
        }

        try {
            $this->userRepository->remove($data);
        } catch (Exception $exception) {
            throw new ApiValidationException('This user has some connected content, so cannot be deleted.');
        }

        return null;
    }
}
