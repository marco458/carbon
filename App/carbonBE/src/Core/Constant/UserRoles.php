<?php

declare(strict_types=1);

namespace Core\Constant;

class UserRoles
{
    final public const ADMIN = 'ROLE_ADMIN';
    final public const USER = 'ROLE_USER';

    final public const ROLES_ARRAY = [self::USER, self::ADMIN];
}
