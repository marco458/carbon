<?php

declare(strict_types=1);

namespace Core\Service;

use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;

final readonly class LockService
{
    public function __construct(
        private LockFactory $factory,
    ) {
    }

    public function getLock(string $key): LockInterface
    {
        return $this->factory->createLock($key);
    }
}
