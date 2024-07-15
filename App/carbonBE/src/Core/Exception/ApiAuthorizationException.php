<?php

declare(strict_types=1);

namespace Core\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

final class ApiAuthorizationException extends VerboseException implements HttpExceptionInterface
{
    public static function create(string $message = '', array $extraData = []): VerboseExceptionInterface
    {
        return new self($message, Response::HTTP_FORBIDDEN, null, $extraData);
    }

    /**
     * Returns the status code.
     *
     * @return int An HTTP response status code
     */
    public function getStatusCode(): int
    {
        return Response::HTTP_UNAUTHORIZED;
    }

    /**
     * Returns response headers.
     *
     * @return array Response headers
     */
    public function getHeaders(): array
    {
        return [];
    }
}
