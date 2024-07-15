<?php

declare(strict_types=1);

namespace Core\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Core\Exception\VerboseException;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;

final class QuerySearchFilter extends AbstractFilter
{
    public function apply(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void {
        if (!is_array($this->properties)) {
            return;
        }

        $query = $context['filters']['query'] ?? '';
        if ('' === $query) {
            return;
        }

        $propertyKeys = array_keys($this->properties);

        $ors = [];
        $parameterName = $queryNameGenerator->generateParameterName('query');
        $metadata = $this->getClassMetadata($context['resource_class']);

        foreach ($propertyKeys as $property) {
            if (!$metadata->hasField($property) || Type::BUILTIN_TYPE_STRING !== $metadata->getTypeOfField($property)) {
                $message = sprintf(
                    'Property %s do not exists on resource %s or is not of type %s',
                    $property,
                    $context['resource_class'],
                    Type::BUILTIN_TYPE_STRING
                );
                throw VerboseException::create($message);
            }
            $ors[] = 'o.'.$property.' LIKE :'.$parameterName;
        }

        $queryBuilder->andWhere($queryBuilder->expr()->orX(...$ors));
        $queryBuilder->setParameter($parameterName, '%'.$query.'%');
    }

    public function getDescription(string $resourceClass): array
    {
        if (!is_array($this->properties)) {
            return [];
        }

        return [
            'query' => [
                'property' => 'query',
                'type' => Type::BUILTIN_TYPE_STRING,
                'required' => false,
                'description' => 'Query string that resource will be filtered by, against all applied properties',
            ],
        ];
    }

    protected function filterProperty(string $property, mixed $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        // We override apply method directly, so we don't need to filter every property
    }
}
