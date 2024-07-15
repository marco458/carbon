<?php

namespace App\Repository\Transportation;

use App\Entity\Transportation\FreightTransportation;
use Core\Repository\BaseRepository;

class FreightTransportationRepository extends BaseRepository
{
    public const ENTITY_CLASS_NAME = FreightTransportation::class;
}
