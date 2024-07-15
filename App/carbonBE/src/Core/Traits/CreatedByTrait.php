<?php

declare(strict_types=1);

namespace Core\Traits;

use Core\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

trait CreatedByTrait
{
    #[
        ORM\ManyToOne(targetEntity: User::class),
        Groups(['item:created_by']),
        ORM\JoinColumn(name: 'created_by_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL'),
    ]
    private ?UserInterface $createdBy = null;

    public function getCreatedBy(): ?UserInterface
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?UserInterface $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}
