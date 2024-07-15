<?php

namespace App\Repository\ElectricalEnergy;

use App\Entity\ElectricalEnergy\ElectricalEnergy;
use Core\Repository\BaseRepository;

class ElectricalEnergyRepository extends BaseRepository
{
    public const ENTITY_CLASS_NAME = ElectricalEnergy::class;
}
