<?php

declare(strict_types=1);

namespace Core\Fixtures\Story;

use Core\Fixtures\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class CoreStory extends Story
{
    public function build(): void
    {
        $this->createUsers();
    }

    private function createUsers(): void
    {
        UserFactory::new()
            ->adminUser()
            ->create();

        UserFactory::new()
            ->normalUser()
            ->create();
    }
}
