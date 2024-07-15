<?php

namespace App\Service\Fixtures;

use App\Entity\AirConditioning\AirConditioning;
use App\Entity\ElectricalEnergy\ElectricalEnergy;
use App\Entity\Fuel\Fuel;
use App\Entity\Gas\Gas;
use App\Entity\Heat\Heat;
use App\Entity\LandConversion\LandConversion;
use App\Entity\Sector\Sector;
use App\Entity\Transportation\FreightTransportation;
use App\Entity\Transportation\PassengerTransportation;
use App\Entity\Unit\Unit;
use App\Entity\Waste\Waste;
use App\Enum\Consumption;
use App\Enum\GasActivity;
use App\Enum\SectorName;
use App\Enum\SubSector;
use App\Repository\AirConditioning\AirConditioningRepository;
use App\Repository\ElectricalEnergy\ElectricalEnergyRepository;
use App\Repository\Fuel\FuelRepository;
use App\Repository\Heat\HeatRepository;
use App\Repository\LandConversion\LandConversionRepository;
use App\Repository\Sector\SectorRepository;
use App\Repository\Transportation\FreightTransportationRepository;
use App\Repository\Transportation\PassengerTransportationRepository;
use App\Repository\Unit\UnitRepository;
use App\Repository\Waste\WasteRepository;
use App\Service\FactorGas\FactorGasService;
use App\Service\FactorUser\FactorUserService;
use Core\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

class FixtureService
{
    public const GAS_CSV = 'assets/gas.csv';
    public const UNIT_CSV = 'assets/unit.csv';

    public const SECTOR_CSV = 'assets/sector.csv';

    public const WASTE_CSV = 'assets/waste.csv';

    public const LAND_CSV = 'assets/land_conversion.csv';

    public const AIR_CSV = 'assets/air_conditioning.csv';

    public const FREIGHT_TRANSPORTATION_CSV = 'assets/freight_transportation.csv';

    public const PASSENGER_TRANSPORTATION_CSV = 'assets/passenger_transportation.csv';

    public const HEAT_CSV = 'assets/heat.csv';

    public const ELECTRICAL_ENERGY_CSV = 'assets/electrical_energy.csv';

    public const FUEL_CSV = 'assets/fuel.csv';

    public function __construct(
        private SectorRepository $sectorRepository,
        private UnitRepository $unitRepository,
        private FactorGasService $factorGasService,
        private FactorUserService $factorUserService,
        private UserRepository $userRepository,
        private FuelRepository $fuelRepository,
        private ElectricalEnergyRepository $electricalEnergyRepository,
        private HeatRepository $heatRepository,
        private PassengerTransportationRepository $passengerTransportationRepository,
        private FreightTransportationRepository $freightTransportationRepository,
        private LandConversionRepository $landConversionRepository,
        private WasteRepository $wasteRepository,
        private AirConditioningRepository $airConditioningRepository,
    ) {
    }

    public function initializeFileAndIo(string $csvFilePath): array
    {
        $io = new SymfonyStyle(new ArrayInput([]), new ConsoleOutput());

        if (!file_exists($csvFilePath)) {
            $io->error('CSV file not found.');

            return [];
        }

        $file = fopen($csvFilePath, 'r');
        if (!$file) {
            $io->error('Failed to open CSV file.');

            return [];
        }

        return [$file];
    }

    public function addGases(ObjectManager $manager): void
    {
        [$file] = $this->initializeFileAndIo(self::GAS_CSV);

        $header = fgetcsv($file); // Assuming first row is header
        while (($row = fgetcsv($file)) !== false) {
            $data = array_combine($header, $row);

            $gas = new Gas();
            $gas->setName(trim($data['name']));
            $gas->setFormula(trim($data['formula']));

            $activity = trim($data['activity']);

            if ('upstream' === $activity) {
                $activity = GasActivity::UPSTREAM;
            } elseif ('waste treatment' === $activity) {
                $activity = GasActivity::WASTE_TREATMENT;
            } elseif ('combustion' === $activity) {
                $activity = GasActivity::COMBUSTION;
            } else {
                $activity = null;
            }
            $gas->setActivity($activity);

            $manager->persist($gas);
        }

        $manager->flush();
    }

    public function addUnits(ObjectManager $manager): void
    {
        [$file] = $this->initializeFileAndIo(self::UNIT_CSV);

        $header = fgetcsv($file); // Assuming first row is header
        while (($row = fgetcsv($file)) !== false) {
            $data = array_combine($header, $row);
            $unit = new Unit();
            $unit->setMeasuringUnit(trim($data['measuringUnit']));
            $manager->persist($unit);
        }

        $manager->flush();
    }

    public function addSectors(ObjectManager $manager): void
    {
        [$file] = $this->initializeFileAndIo(self::SECTOR_CSV);

        $header = fgetcsv($file); // Assuming first row is header
        while (($row = fgetcsv($file)) !== false) {
            $data = array_combine($header, $row);
            $sector = new Sector();
            $name = trim($data['name']);

            if ('fuel' === $name) {
                $name = SectorName::FUEL;
            } elseif ('electrical energy' === $name) {
                $name = SectorName::ELECTRICAL_ENERGY;
            } elseif ('heat' === $name) {
                $name = SectorName::HEAT;
            } elseif ('passenger transportation' === $name) {
                $name = SectorName::PASSENGER_TRANSPORTATION;
            } elseif ('freight transportation' === $name) {
                $name = SectorName::FREIGHT_TRANSPORTATION;
            } elseif ('land conversion' === $name) {
                $name = SectorName::LAND_CONVERSION;
            } elseif ('waste' === $name) {
                $name = SectorName::WASTE;
            } elseif ('air conditioning' === $name) {
                $name = SectorName::AIR_CONDITIONING;
            }

            $sector->setName($name);
            $manager->persist($sector);
        }

        $manager->flush();
    }

    public function addWastes(ObjectManager $manager): void
    {
        [$file] = $this->initializeFileAndIo(self::WASTE_CSV);

        $header = fgetcsv($file); // Assuming first row is header
        while (($row = fgetcsv($file)) !== false) {
            $data = array_combine($header, $row);

            $waste = new Waste();
            $waste->setSector($this->sectorRepository->findOneBy(['name' => SectorName::WASTE]));
            $waste->setUnit($this->unitRepository->findOneBy(['measuringUnit' => 'Kg/t']));
            $waste->setCategory($data['category']);

            $manager->persist($waste);
            $manager->flush();

            // add gasses to factor
            $this->factorGasService->appendGasesToFactor($data, $waste->getId(), 'waste');
        }

        $manager->flush();
    }

    public function addLandConversion(ObjectManager $manager): void
    {
        [$file] = $this->initializeFileAndIo(self::LAND_CSV);

        $header = fgetcsv($file); // Assuming first row is header
        while (($row = fgetcsv($file)) !== false) {
            $data = array_combine($header, $row);

            $land = new LandConversion();
            $land->setSector($this->sectorRepository->findOneBy(['name' => SectorName::LAND_CONVERSION]));
            $land->setUnit($this->unitRepository->findOneBy(['measuringUnit' => 'kg/ha']));
            $land->setCategory($data['category']);

            $manager->persist($land);
            $manager->flush();

            $this->factorGasService->appendGasesToFactor($data, $land->getId(), 'land');
        }

        $manager->flush();
    }

    public function addAirConditionings(ObjectManager $manager): void
    {
        [$file] = $this->initializeFileAndIo(self::AIR_CSV);

        $header = fgetcsv($file); // Assuming first row is header
        while (($row = fgetcsv($file)) !== false) {
            $data = array_combine($header, $row);

            $air = new AirConditioning();
            $air->setSector($this->sectorRepository->findOneBy(['name' => SectorName::AIR_CONDITIONING]));
            $air->setUnit($this->unitRepository->findOneBy(['measuringUnit' => 'kg/g']));
            $air->setCategory($data['category']);

            $manager->persist($air);
            $manager->flush();

            $this->factorGasService->appendGasesToFactor($data, $air->getId(), 'air');
        }

        $manager->flush();
    }

    public function addFreightTransportations(ObjectManager $manager): void
    {
        [$file] = $this->initializeFileAndIo(self::FREIGHT_TRANSPORTATION_CSV);

        $header = fgetcsv($file); // Assuming first row is header
        while (($row = fgetcsv($file)) !== false) {
            $data = array_combine($header, $row);

            $freight = new FreightTransportation();
            $freight->setSector($this->sectorRepository->findOneBy(['name' => SectorName::FREIGHT_TRANSPORTATION]));
            $freight->setUnit($this->unitRepository->findOneBy(['measuringUnit' => 'kg/g']));

            $freight->setVehicleType($data['vehicle_type']);
            $freight->setFuelAndLoad($data['fuel_and_load']);
            $freight->setEuroStandard($data['euro_standard']);

            $manager->persist($freight);
            $manager->flush();

            $this->factorGasService->appendGasesToFactor($data, $freight->getId(), 'freight');
        }

        $manager->flush();
    }

    public function addPassengerTransportations(ObjectManager $manager): void
    {
        [$file] = $this->initializeFileAndIo(self::PASSENGER_TRANSPORTATION_CSV);

        $header = fgetcsv($file); // Assuming first row is header
        while (($row = fgetcsv($file)) !== false) {
            $data = array_combine($header, $row);

            $passenger = new PassengerTransportation();
            $passenger->setSector($this->sectorRepository->findOneBy(['name' => SectorName::PASSENGER_TRANSPORTATION]));

            if ('Autobus' === $data['vehicle_type']) {
                $passenger->setUnit($this->unitRepository->findOneBy(['measuringUnit' => 'kg/km']));
            } else {
                $passenger->setUnit($this->unitRepository->findOneBy(['measuringUnit' => 'kg/vehicle and km']));
            }

            $passenger->setVehicleType($data['vehicle_type']);
            $passenger->setVehicleClass($data['vehicle_class']);
            $passenger->setFuel($data['fuel']);
            $passenger->setEuroStandard($data['euro_standard']);

            $manager->persist($passenger);
            $manager->flush();

            $this->factorGasService->appendGasesToFactor($data, $passenger->getId(), 'passenger');
        }

        $manager->flush();
    }

    public function addHeats(ObjectManager $manager): void
    {
        [$file] = $this->initializeFileAndIo(self::HEAT_CSV);

        $header = fgetcsv($file); // Assuming first row is header
        $i = 0;
        while (($row = fgetcsv($file)) !== false) {
            $data = array_combine($header, $row);

            $heat = new Heat();
            $heat->setSector($this->sectorRepository->findOneBy(['name' => SectorName::HEAT]));

            if (0 === $i % 2) {
                $heat->setUnit($this->unitRepository->findOneBy(['measuringUnit' => 'kg/MWh']));
            } else {
                $heat->setUnit($this->unitRepository->findOneBy(['measuringUnit' => 'kg/GJ']));
            }

            if ('Public heating plants' === $data['sub_sector']) {
                $heat->setSubSector(SubSector::PUBLIC_HEATING_PLANTS);
            } elseif ('Heat production systems' === $data['sub_sector']) {
                $heat->setSubSector(SubSector::HEAT_PRODUCTION_SYSTEMS);
            } else {
                $heat->setSubSector(SubSector::PUBLIC_BOILER_HOUSES);
            }

            $heat->setEnergyType($data['energy_type']);

            if ('' !== $data['location']) {
                $heat->setLocation($data['location']);
            }
            if ('' !== $data['technology']) {
                $heat->setTechnology($data['technology']);
            }

            $manager->persist($heat);
            $manager->flush();

            $this->factorGasService->appendGasesToFactor($data, $heat->getId(), 'heat');
            ++$i;
        }

        $manager->flush();
    }

    public function addEnergies(ObjectManager $manager): void
    {
        [$file] = $this->initializeFileAndIo(self::ELECTRICAL_ENERGY_CSV);

        $header = fgetcsv($file); // Assuming first row is header
        $i = 0;
        while (($row = fgetcsv($file)) !== false) {
            $data = array_combine($header, $row);

            $energy = new ElectricalEnergy();
            $energy->setSector($this->sectorRepository->findOneBy(['name' => SectorName::ELECTRICAL_ENERGY]));

            if (0 === $i % 2) {
                $energy->setUnit($this->unitRepository->findOneBy(['measuringUnit' => 'kg/MWh']));
            } else {
                $energy->setUnit($this->unitRepository->findOneBy(['measuringUnit' => 'kg/GJ']));
            }

            if ('average consumption' === $data['sub_sector']) {
                $energy->setSubSector(SubSector::AVERAGE_CONSUMPTION);
            } elseif ('renewable sources' === $data['sub_sector']) {
                $energy->setSubSector(SubSector::RENEWABLE_SOURCES);
            } else {
                $energy->setSubSector(SubSector::RENEWABLE_POWER_PLANT);
            }

            $energy->setEnergyType($data['energy_type']);

            if ('' !== $data['year']) {
                $energy->setYear($data['year']);
            }
            if ('' !== $data['power_plant_type']) {
                $energy->setPowerPlantType($data['power_plant_type']);
            }

            $manager->persist($energy);
            $manager->flush();

            $this->factorGasService->appendGasesToFactor($data, $energy->getId(), 'energy');
            ++$i;
        }

        $manager->flush();
    }

    public function addFuels(ObjectManager $manager): void
    {
        [$file] = $this->initializeFileAndIo(self::FUEL_CSV);

        $header = fgetcsv($file); // Assuming first row is header
        $i = 0;
        while (($row = fgetcsv($file)) !== false) {
            $data = array_combine($header, $row);

            $fuel = new Fuel();
            $fuel->setSector($this->sectorRepository->findOneBy(['name' => SectorName::FUEL]));

            if (0 === $i % 2) {
                $fuel->setUnit($this->unitRepository->findOneBy(['measuringUnit' => 'kg/MWh']));
            } else {
                $fuel->setUnit($this->unitRepository->findOneBy(['measuringUnit' => 'kg/GJ']));
            }

            if ('fossil fuels' === $data['sub_sector']) {
                $fuel->setSubSector(SubSector::FOSSIL_FUELS);
            } elseif ('biomass green houses' === $data['sub_sector']) {
                $fuel->setSubSector(SubSector::BIOMASS_GREENHOUSE_GASES);
            } else {
                $fuel->setSubSector(SubSector::BIOMASS_BIOGENIC_EMISSIONS);
            }

            $fuel->setFuelGroup($data['fuel_group']);
            $fuel->setFuelType($data['fuel_type']);
            $fuel->setTypeOfEnergySource($data['type_of_energy_source']);

            $manager->persist($fuel);
            $manager->flush();

            $this->factorGasService->appendGasesToFactor($data, $fuel->getId(), 'fuel');
            ++$i;
        }

        $manager->flush();
    }

    public function addDummyDataToUser(ObjectManager $manager): void
    {
        // load units
        /** @var Unit $kgGj */
        $kgGj = $this->unitRepository->findOneBy(['measuringUnit' => 'Kg/GJ']);
        /** @var Unit $kgMwh */
        $kgMwh = $this->unitRepository->findOneBy(['measuringUnit' => 'kg/MWh']);
        /** @var Unit $kgVehicleKm */
        $kgVehicleKm = $this->unitRepository->findOneBy(['measuringUnit' => 'kg/vehicle and km']);
        $userId = $this->userRepository->findOneBy(['email' => 'admin@admin.com'])->getId();

        // primjer 1
        $factorId = $this->fuelRepository->findOneBy([
            'fuelGroup' => 'Plinovita goriva',
            'fuelType' => 'Prirodni plin',
            'typeOfEnergySource' => 'Nepokretni',
            'unit' => $kgMwh,
        ])->getId();

        $this->factorUserService->createEntryForUser($userId, $factorId, 'fuel', 500, 2024, GasActivity::COMBUSTION, Consumption::DIRECT, $kgMwh->getMeasuringUnit());

        // primjer 2
        $factorId = $this->fuelRepository->findOneBy([
            'fuelGroup' => 'Tekuća goriva',
            'fuelType' => 'Motorni benzin',
            'typeOfEnergySource' => 'Pokretni',
            'unit' => $kgGj,
        ])->getId();

        $this->factorUserService->createEntryForUser($userId, $factorId, 'fuel', 900, 2024, GasActivity::COMBUSTION, Consumption::INDIRECT, $kgGj->getMeasuringUnit());

        // primjer 3
        $factorId = $this->electricalEnergyRepository->findOneBy([
            'year' => 2020,
            'unit' => $kgMwh,
            'subSector' => SubSector::AVERAGE_CONSUMPTION,
        ])->getId();

        $this->factorUserService->createEntryForUser($userId, $factorId, 'energy', 1500, 2024, GasActivity::COMBUSTION, Consumption::INDIRECT, $kgMwh->getMeasuringUnit());

        // primjer 4
        $factorId = $this->heatRepository->findOneBy([
            'location' => 'Zagreb',
            'unit' => $kgMwh,
            'subSector' => SubSector::PUBLIC_HEATING_PLANTS,
        ])->getId();

        $this->factorUserService->createEntryForUser($userId, $factorId, 'heat', 800, 2024, GasActivity::COMBUSTION, Consumption::DIRECT, $kgMwh->getMeasuringUnit());

        // primjer 5
        $factorId = $this->fuelRepository->findOneBy([
            'fuelGroup' => 'Tekuća goriva',
            'fuelType' => 'Dizelsko gorivo',
            'unit' => $kgGj,
            'typeOfEnergySource' => 'Pokretni',
            'subSector' => SubSector::FOSSIL_FUELS,
        ])->getId();

        $this->factorUserService->createEntryForUser($userId, $factorId, 'fuel', 1200, 2024, null, Consumption::DIRECT, $kgGj->getMeasuringUnit());

        // primjer 6
        $factorId = $this->fuelRepository->findOneBy([
            'fuelGroup' => 'Kruta goriva',
            'fuelType' => 'Drvna sječka',
            'unit' => $kgGj,
            'typeOfEnergySource' => 'Nepokretni',
            'subSector' => SubSector::BIOMASS_GREENHOUSE_GASES,
        ])->getId();

        $this->factorUserService->createEntryForUser($userId, $factorId, 'fuel', 2000, 2024, GasActivity::COMBUSTION, Consumption::INDIRECT, $kgGj->getMeasuringUnit());

        // primjer 7
        $factorId = $this->passengerTransportationRepository->findOneBy([
            'vehicleType' => 'Osobno vozilo',
            'fuel' => 'Benzin',
            'vehicleClass' => 'Srednja klasa vozila',
            'euroStandard' => 'EURO 6',
            'unit' => $kgVehicleKm,
        ])->getId();

        $this->factorUserService->createEntryForUser($userId, $factorId, 'passenger', 120000, 2024, GasActivity::COMBUSTION, Consumption::INDIRECT, $kgVehicleKm->getMeasuringUnit());

        $manager->flush();

        // my custom examples
        $factorId = $this->freightTransportationRepository->findOneBy([
            'vehicleType' => 'Teško teretno vozilo',
            'fuelAndLoad' => 'Dizel- kruto 7.5 - 20 t',
            'euroStandard' => 'EURO 6',
        ])->getId();

        $this->factorUserService->createEntryForUser($userId, $factorId, 'freight', 50000, 2024, GasActivity::COMBUSTION, Consumption::INDIRECT);

        $manager->flush();

        $factorId = $this->airConditioningRepository->findOneBy([
            'category' => 'Rashladni uređaji za kućanstvo',
        ])->getId();

        $this->factorUserService->createEntryForUser($userId, $factorId, 'air', 1000, 2024, null, Consumption::INDIRECT);

        $manager->flush();

        $factorId = $this->landConversionRepository->findOneBy([
            'category' => 'Travnjaci pretvoreni u Naseljena područja',
        ])->getId();

        $this->factorUserService->createEntryForUser($userId, $factorId, 'land', 15, 2024, null, Consumption::INDIRECT);

        $manager->flush();

        $factorId = $this->wasteRepository->findOneBy([
            'category' => 'Odlaganje otpada na uređena odlagališta',
        ])->getId();

        $this->factorUserService->createEntryForUser($userId, $factorId, 'waste', 35, 2024, null, Consumption::INDIRECT);

        $manager->flush();

        // Add for previous years ************************************************************++
        // primjer 1
        $factorId = $this->fuelRepository->findOneBy([
            'fuelGroup' => 'Plinovita goriva',
            'fuelType' => 'Prirodni plin',
            'typeOfEnergySource' => 'Nepokretni',
            'unit' => $kgMwh,
        ])->getId();

        $this->factorUserService->createEntryForUser($userId, $factorId, 'fuel', 700, 2023, GasActivity::COMBUSTION, Consumption::INDIRECT, $kgMwh->getMeasuringUnit());

        // primjer 2
        $factorId = $this->fuelRepository->findOneBy([
            'fuelGroup' => 'Tekuća goriva',
            'fuelType' => 'Motorni benzin',
            'typeOfEnergySource' => 'Pokretni',
            'unit' => $kgGj,
        ])->getId();

        $this->factorUserService->createEntryForUser($userId, $factorId, 'fuel', 990, 2023, GasActivity::COMBUSTION, Consumption::DIRECT, $kgGj->getMeasuringUnit());

        // primjer 3
        $factorId = $this->electricalEnergyRepository->findOneBy([
            'year' => 2020,
            'unit' => $kgMwh,
            'subSector' => SubSector::AVERAGE_CONSUMPTION,
        ])->getId();

        $this->factorUserService->createEntryForUser($userId, $factorId, 'energy', 2500, 2023, GasActivity::COMBUSTION, Consumption::DIRECT, $kgMwh->getMeasuringUnit());

        // primjer 4
        $factorId = $this->heatRepository->findOneBy([
            'location' => 'Zagreb',
            'unit' => $kgMwh,
            'subSector' => SubSector::PUBLIC_HEATING_PLANTS,
        ])->getId();

        $this->factorUserService->createEntryForUser($userId, $factorId, 'heat', 800, 2023, GasActivity::COMBUSTION, Consumption::DIRECT, $kgMwh->getMeasuringUnit());

        // primjer 5
        $factorId = $this->fuelRepository->findOneBy([
            'fuelGroup' => 'Tekuća goriva',
            'fuelType' => 'Dizelsko gorivo',
            'unit' => $kgGj,
            'typeOfEnergySource' => 'Pokretni',
            'subSector' => SubSector::FOSSIL_FUELS,
        ])->getId();

        $this->factorUserService->createEntryForUser($userId, $factorId, 'fuel', 1200, 2023, null, Consumption::DIRECT, $kgGj->getMeasuringUnit());

        // primjer 6
        $factorId = $this->fuelRepository->findOneBy([
            'fuelGroup' => 'Kruta goriva',
            'fuelType' => 'Drvna sječka',
            'unit' => $kgGj,
            'typeOfEnergySource' => 'Nepokretni',
            'subSector' => SubSector::BIOMASS_GREENHOUSE_GASES,
        ])->getId();

        $this->factorUserService->createEntryForUser($userId, $factorId, 'fuel', 2300, 2023, GasActivity::COMBUSTION, Consumption::INDIRECT, $kgGj->getMeasuringUnit());

        // primjer 7
        $factorId = $this->passengerTransportationRepository->findOneBy([
            'vehicleType' => 'Osobno vozilo',
            'fuel' => 'Benzin',
            'vehicleClass' => 'Srednja klasa vozila',
            'euroStandard' => 'EURO 6',
            'unit' => $kgVehicleKm,
        ])->getId();

        $this->factorUserService->createEntryForUser($userId, $factorId, 'passenger', 120000, 2023, GasActivity::COMBUSTION, Consumption::DIRECT, $kgVehicleKm->getMeasuringUnit());

        $manager->flush();

        // my custom examples
        $factorId = $this->freightTransportationRepository->findOneBy([
            'vehicleType' => 'Teško teretno vozilo',
            'fuelAndLoad' => 'Dizel- kruto 7.5 - 20 t',
            'euroStandard' => 'EURO 6',
        ])->getId();

        $this->factorUserService->createEntryForUser($userId, $factorId, 'freight', 50000, 2023, GasActivity::COMBUSTION, Consumption::INDIRECT);

        $manager->flush();

        $factorId = $this->airConditioningRepository->findOneBy([
            'category' => 'Rashladni uređaji za kućanstvo',
        ])->getId();

        $this->factorUserService->createEntryForUser($userId, $factorId, 'air', 1000, 2023, null, Consumption::DIRECT);

        $manager->flush();

        $factorId = $this->landConversionRepository->findOneBy([
            'category' => 'Travnjaci pretvoreni u Naseljena područja',
        ])->getId();

        $this->factorUserService->createEntryForUser($userId, $factorId, 'land', 15, 2023, null, Consumption::DIRECT);

        $manager->flush();

        $factorId = $this->wasteRepository->findOneBy([
            'category' => 'Odlaganje otpada na uređena odlagališta',
        ])->getId();

        $this->factorUserService->createEntryForUser($userId, $factorId, 'waste', 35, 2023, null, Consumption::INDIRECT);

        $manager->flush();
    }
}
