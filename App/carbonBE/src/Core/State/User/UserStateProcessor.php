<?php

declare(strict_types=1);

namespace Core\State\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Carbon\Carbon;
use Core\Constant\UserRoles;
use Core\Entity\User\User;
use Core\Exception\ApiInvalidResourceException;
use Core\Exception\ApiValidationException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class UserStateProcessor implements ProcessorInterface
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        private ProcessorInterface $persistProcessor
    ) {
    }

    /**
     * @throws ApiValidationException
     * @throws ApiInvalidResourceException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): User
    {
        if (!$data instanceof User) {
            throw new ApiInvalidResourceException('Expecting resource to be User');
        }

        if (!in_array($data->getRoles()[0], UserRoles::ROLES_ARRAY, true)) {
            throw new ApiValidationException('Role not allowed, please select one of the defined ones.');
        }

        if ((bool) $data->getPlaintextPassword()) {
            $hashedPassword = $this->userPasswordHasher->hashPassword($data, trim((string) $data->getPlaintextPassword()));
            $data->setPassword($hashedPassword);

            if (($operation->getName() ?? '') === 'api_v1_reset_password') {
                $data->setPasswordResetToken(null);
            }
        }

        $data->setActive(true);
        $data->setEmailConfirmedAt(Carbon::now());

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
