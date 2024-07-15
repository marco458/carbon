<?php

declare(strict_types=1);

namespace Core\Repository;

use ApiPlatform\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Metadata\Operation;
use Core\Entity\EntityInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * BaseRepository.
 *
 * @author Ante Crnogorac<ante@q.agency>
 */
abstract class BaseRepository extends EntityRepository implements ServiceEntityRepositoryInterface
{
    public const ENTITY_CLASS_NAME = '';

    public function __construct(
        EntityManagerInterface $entityManager,
        null|ClassMetadata $metadata = null,
        null|ManagerRegistry $registry = null,
    ) {
        if ('' === $this::ENTITY_CLASS_NAME) {
            throw new \RuntimeException('Repository entity class name is empty');
        }

        if ($registry instanceof ManagerRegistry) {
            /** @var EntityManager $manager */
            $manager = $registry->getManagerForClass($this::ENTITY_CLASS_NAME);
            parent::__construct($manager, $manager->getClassMetadata($this::ENTITY_CLASS_NAME));
        } elseif ($entityManager instanceof EntityManager && $metadata instanceof ClassMetadata) {
            parent::__construct($entityManager, $metadata);
        } else {
            throw new \RuntimeException('Failed to initialize repository.');
        }
    }

    public function save(EntityInterface $entity, bool $flush = true): EntityInterface
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }

        return $entity;
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    public function remove(EntityInterface $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findOr404(int $id): EntityInterface
    {
        /** @var EntityInterface|null $entity */
        $entity = $this->find($id);

        if (!$entity instanceof EntityInterface) {
            $message = sprintf('Resource of type %s and ID %s could not be found!', $this::ENTITY_CLASS_NAME, $id);
            throw new NotFoundHttpException($message, null, Response::HTTP_NOT_FOUND);
        }

        return $entity;
    }

    protected function applyApiPlatformExtensionsToCollection(iterable $apiPlatformExtensions, QueryBuilder $qb, array $context, Operation $operation): iterable
    {
        $queryNameGenerator = new QueryNameGenerator();

        foreach ($apiPlatformExtensions as $extension) {
            $extension->applyToCollection($qb, $queryNameGenerator, $context['resource_class'], $operation, $context);

            // Native CollectionProvider class here checks for instance of QueryResultCollectionExtensionInterface, but phpstan says no! :D
            if ($extension instanceof QueryResultCollectionExtensionInterface &&
                $extension->supportsResult($context['resource_class'], $operation, $context)) {
                return $extension->getResult($qb, $context['resource_class'], $operation, $context);
            }
        }

        /* @phpstan-ignore-next-line */
        return $qb->getQuery()->getResult();
    }
}
