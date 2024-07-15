<?php

namespace App\Repository\Transportation;

use App\Entity\Transportation\PassengerTransportation;
use Core\Repository\BaseRepository;

class PassengerTransportationRepository extends BaseRepository
{
    public const ENTITY_CLASS_NAME = PassengerTransportation::class;
}
