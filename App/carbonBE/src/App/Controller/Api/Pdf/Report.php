<?php

namespace App\Controller\Api\Pdf;

use App\Repository\Location\LocationRepository;
use App\Request\GenerateReportRequest;
use App\Service\Export\ExportService;
use App\Service\Export\GraphService;
use Carbon\Carbon;
use Core\Service\CurrentUserResolver;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class Report extends AbstractController
{
    public function __construct(
        private ExportService $exportService,
        private GraphService $graphService,
        private CurrentUserResolver $currentUserResolver,
        private LocationRepository $locationRepository
    ) {
    }

    public function __invoke(Pdf $knpSnappyPdf, GenerateReportRequest $request): Response
    {
        $userData = $this->exportService->prepareUserData($request);

        $scopeData = $this->exportService->calculateDistributionByScopes($userData);
        $sectorData = $this->exportService->calculateDistributionBySectors($userData);
        $gasData = $this->exportService->calculateDistributionByGases($userData);

        $graphPieScopeValue = $this->graphService->prepareGraphScopeValue($scopeData);
        $graphPieScopePercentage = $this->graphService->prepareGraphScopePercentage($scopeData);

        $graphBarSectorValue = $this->graphService->prepareGraphSectorValue($sectorData);
        $graphBarSectorPercentage = $this->graphService->prepareGraphSectorPercentage($sectorData);

        $graphBarGasValue = $this->graphService->prepareGraphGasValue($gasData);
        $graphBarGasPercentage = $this->graphService->prepareGraphGasPercentage($gasData);

        $allGasTotalT = round($gasData['all_gas_total_value'] / 1000, 2);

        $scope1Percentage = round($scopeData['scope_1_percentage'], 2);
        $scope2Percentage = round($scopeData['scope_2_percentage'], 2);
        $scope3Percentage = round($scopeData['scope_3_percentage'], 2);

        $location = is_null($request->getLocationId()) ? 'Cijela organizacija' : $this->locationRepository->find($request->getLocationId())->getName();
        $html = $this->renderView('pdf/report.html.twig', [
            'user' => $this->currentUserResolver->resolve(),
            'today' => Carbon::today(),
            'startDate' => $request->getFromDate() ?? '',
            'endDate' => $request->getToDate() ?? '',
            'location' => $location,
            'totalCO2' => $allGasTotalT,
            'scope1Percentage' => $scope1Percentage,
            'scope2Percentage' => $scope2Percentage,
            'scope3Percentage' => $scope3Percentage,
            'graphBarSectorValue' => $graphBarSectorValue,
            'graphBarSectorPercentage' => $graphBarSectorPercentage,
            'graphPieScopeValue' => $graphPieScopeValue,
            'graphPieScopePercentage' => $graphPieScopePercentage,
            'graphBarGasValue' => $graphBarGasValue,
            'graphBarGasPercentage' => $graphBarGasPercentage,
        ]);

        return new PdfResponse(
            $knpSnappyPdf->getOutputFromHtml($html),
            'izvjestaj.pdf',
            'application/ pdf',
            'attachment',
            200,
            []
        );
    }
}
