<?php

declare(strict_types=1);

namespace Core\Traits;

use Carbon\Carbon;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

trait TimestampableTrait
{
    #[
        ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE, nullable: true),
        Groups(['item:timestamps', 'login']),
        SerializedName('created_at')
    ]
    private ?\DateTimeInterface $createdAt = null;

    #[
        ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE, nullable: true),
        Groups(['item:timestamps', 'login']),
        SerializedName('updated_at')
    ]
    private ?\DateTimeInterface $updatedAt = null;

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    #[ORM\PreUpdate]
    public function onUpdate(): self
    {
        $this->updatedAt = new Carbon('now');

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function onCreate(): self
    {
        $this->createdAt = new Carbon('now');
        $this->updatedAt = new Carbon('now');

        return $this;
    }
}
