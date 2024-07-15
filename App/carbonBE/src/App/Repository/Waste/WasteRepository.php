<?php

namespace App\Repository\Waste;

use App\Entity\Waste\Waste;
use Core\Repository\BaseRepository;

class WasteRepository extends BaseRepository
{
    public const ENTITY_CLASS_NAME = Waste::class;
}
