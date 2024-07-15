<?php

declare(strict_types=1);

namespace Core\Exception;

use Symfony\Component\HttpFoundation\Response;

class VerboseException extends \Exception implements VerboseExceptionInterface
{
    public function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
        public array $extraData = [],
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getExtraData(): array
    {
        return $this->extraData;
    }

    public static function create(string $message = '', array $extraData = []): VerboseExceptionInterface
    {
        return new self($message, Response::HTTP_UNPROCESSABLE_ENTITY, null, $extraData);
    }
}
