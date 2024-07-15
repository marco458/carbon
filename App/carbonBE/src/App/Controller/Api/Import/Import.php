<?php

namespace App\Controller\Api\Import;

use App\Constant\SupportedFqcn;
use App\Entity\Factor\FactorUser;
use App\Enum\Consumption;
use App\Enum\GasActivity;
use App\Repository\Factor\FactorUserRepository;
use Carbon\Carbon;
use Core\Service\CurrentUserResolver;
use Core\Service\FileReader\CsvFileReaderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class Import extends AbstractController
{
    public function __construct(
        private CsvFileReaderService $csvFileReader,
        private FactorUserRepository $factorUserRepository,
        private CurrentUserResolver $userResolver
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $uploadedFile = $request->files->get('file');
        $user = $this->userResolver->resolve();
        $data = $this->csvFileReader->read($uploadedFile);
        foreach ($data as $element) {
            $activity = $element['gas_activity'];
            $consumption = $element['consumption'];

            $factorUser = new FactorUser();
            $factorUser->setUser($user);
            $factorUser->setFactorId((int) $element['factor_id']);
            $factorUser->setFactorFqcn(SupportedFqcn::mapClassNameToFqcn($element['sector']));
            $factorUser->setAmount((float) $element['amount']);
            $factorUser->setDate(Carbon::create((int) $element['year'], 1, 1));
            $factorUser->setUnit($element['unit']);

            if ('upstream' === $activity) {
                $activity = GasActivity::UPSTREAM;
            } elseif ('waste treatment' === $activity) {
                $activity = GasActivity::WASTE_TREATMENT;
            } elseif ('combustion' === $activity) {
                $activity = GasActivity::COMBUSTION;
            } else {
                $activity = null;
            }
            $factorUser->setGasActivity($activity);

            if ('direct' === $consumption) {
                $consumption = Consumption::DIRECT;
            } elseif ('indirect' === $consumption) {
                $consumption = Consumption::INDIRECT;
            } else {
                $consumption = null;
            }
            $factorUser->setConsumption($consumption);

            $this->factorUserRepository->save($factorUser);
        }

        return new JsonResponse([]);
    }
}
