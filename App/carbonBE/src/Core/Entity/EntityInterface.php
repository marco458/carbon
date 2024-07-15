<?php

declare(strict_types=1);

namespace Core\Entity;

/**
 * Interface EntityInterface.
 *
 * @author Zwer<ante@q-software.com>
 */
interface EntityInterface
{
    public function getId(): int|string|null;
}
