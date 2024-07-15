<?php

declare(strict_types=1);

namespace Core\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

final class ApiValidationException extends VerboseException implements HttpExceptionInterface
{
    public static function create(string $message = '', array $extraData = []): VerboseExceptionInterface
    {
        return new self($message, Response::HTTP_UNPROCESSABLE_ENTITY, null, $extraData);
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_UNPROCESSABLE_ENTITY;
    }

    public function getHeaders(): array
    {
        return [];
    }
}
