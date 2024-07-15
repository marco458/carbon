<?php

namespace App\Entity\Factor;

use ApiPlatform\Metadata\ApiProperty;
use App\Entity\Sector\Sector;
use App\Entity\Unit\Unit;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

class Factor
{
    #[
        ORM\ManyToOne(fetch: 'EAGER'),
        ORM\JoinColumn(nullable: false),
        Groups(['factor:create', 'factor:get']),
        ApiProperty(example: '/api/v1/units/1')
    ]
    protected ?Unit $unit = null;

    #[
        ORM\ManyToOne(fetch: 'EAGER'),
        ORM\JoinColumn(nullable: false),
        Groups(['factor:create', 'factor:get']),
        ApiProperty(example: '/api/v1/sectors/1')
    ]
    protected ?Sector $sector = null;

    #[
        Groups(['factor:create', 'factor:get']),
        ApiProperty(example: ['/api/v1/gases/1', '/api/v1/gases/2'])
    ]
    protected ?array $gases = [];

    #[
        Groups(['factor:create', 'factor:get']),
        ApiProperty(example: 'waste')
    ]
    protected string $className;

    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    public function setUnit(?Unit $unit): static
    {
        $this->unit = $unit;

        return $this;
    }

    public function getSector(): ?Sector
    {
        return $this->sector;
    }

    public function setSector(?Sector $sector): self
    {
        $this->sector = $sector;

        return $this;
    }

    public function getGases(): ?array
    {
        return $this->gases;
    }

    public function setGases(?array $gases): self
    {
        $this->gases = $gases;

        return $this;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function setClassName(string $className): self
    {
        $this->className = $className;

        return $this;
    }
}
