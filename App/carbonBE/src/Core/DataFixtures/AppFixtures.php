<?php

declare(strict_types=1);

namespace Core\DataFixtures;

use App\Service\Fixtures\FixtureService;
use Core\Fixtures\Story\CoreStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class AppFixtures extends Fixture implements OrderedFixtureInterface
{
    public function __construct(
        private FixtureService $fixtureService,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        CoreStory::load();

        $manager->flush();

        $this->fixtureService->addGases($manager);
        $this->fixtureService->addUnits($manager);
        $this->fixtureService->addSectors($manager);
        $this->fixtureService->addWastes($manager);
        $this->fixtureService->addLandConversion($manager);
        $this->fixtureService->addAirConditionings($manager);
        $this->fixtureService->addFreightTransportations($manager);
        $this->fixtureService->addPassengerTransportations($manager);
        $this->fixtureService->addHeats($manager);
        $this->fixtureService->addEnergies($manager);
        $this->fixtureService->addFuels($manager);

        // add dummy data for user
        $this->fixtureService->addDummyDataToUser($manager);
    }

    public function getOrder(): int
    {
        return 10;
    }
}
