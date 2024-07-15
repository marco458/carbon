<?php

namespace App\Entity\ApiResource\Pdf;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Controller\Api\Pdf\Report;
use App\Request\GenerateReportRequest;

#[ApiResource(
    shortName: 'Pdf',
    operations: [
        new Post(
            uriTemplate: '/v1/report',
            controller: Report::class,
            security: 'is_granted("ROLE_USER")',
            input: GenerateReportRequest::class,
            name: 'api_v1_export_pdf_test',
        ),
    ]
)
]
class PdfResource
{
}
