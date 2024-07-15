<?php

declare(strict_types=1);

namespace Core\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

final class ApiNotFoundException extends VerboseException implements HttpExceptionInterface
{
    public static function create(string $message = '', array $extraData = []): VerboseExceptionInterface
    {
        return new self($message, Response::HTTP_NOT_FOUND, null, $extraData);
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }

    public function getHeaders(): array
    {
        return [];
    }
}
