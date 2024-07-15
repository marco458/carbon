<?php

namespace App\Entity\ApiResource\Import;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Controller\Api\Import\Import;

#[ApiResource(
    shortName: 'Import',
    operations: [
        new Post(
            uriTemplate: '/v1/import',
            controller: Import::class,
            openapiContext: [
                'requestBody' => [
                    'content' => [
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'file' => [
                                        'type' => 'string',
                                        'format' => 'binary',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            deserialize: false,
            security: 'is_granted("ROLE_USER")',
            name: 'api_v1_import_factor_user',
        ),
    ]
)
]
class ImportResource
{
}
