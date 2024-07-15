<?php

declare(strict_types=1);

namespace Core\Service;

final class TraitHelper
{
    /**
     * For the passed class returns an array of traits used by it and/or its parent(s).
     *
     * @param string $class - must be a fully qualified class name
     */
    public static function classUsesDeep(string $class): array
    {
        $traits = class_uses($class);

        if (false === $traits || [] === $traits) {
            return [];
        }

        $parent = get_parent_class($class);

        if (false !== $parent) {
            $traits += self::classUsesDeep($parent);
        }

        return $traits;
    }
}
