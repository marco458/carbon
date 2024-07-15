<?php

declare(strict_types=1);

namespace Core\Factory;

use Core\Entity\EntityInterface;
use Core\Exception\VerboseExceptionInterface;
use Core\Service\ValidatorService;
use Symfony\Component\Serializer\SerializerInterface;

final readonly class EntityFactory
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorService $validator,
    ) {
    }

    public function createFromJson(string $data, string $class, array $groups = [], array $context = []): EntityInterface
    {
        return $this->create($data, $class, 'json', $groups, $context);
    }

    public function createFromArray(array $data, string $class, array $groups = [], array $context = []): EntityInterface
    {
        $jsonData = json_encode($data, JSON_THROW_ON_ERROR);

        return $this->create($jsonData, $class, 'json', $groups, $context);
    }

    /**
     * @throws VerboseExceptionInterface
     */
    private function create(string $data, string $class, string $format, array $groups = [], array $context = []): EntityInterface
    {
        if ([] !== $groups) {
            $context['groups'] = $groups;
        }

        /** @var EntityInterface $entity */
        $entity = $this->serializer->deserialize($data, $class, $format, $context);

        $this->validator->validate($entity, $groups);

        return $entity;
    }
}
