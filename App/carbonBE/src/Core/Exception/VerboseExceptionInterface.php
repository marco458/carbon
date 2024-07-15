<?php

declare(strict_types=1);

namespace Core\Exception;

interface VerboseExceptionInterface extends \Throwable
{
    /**
     * VerboseException constructor.
     */
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null, array $extraData = []);

    public function getExtraData(): array;

    public static function create(string $message = '', array $extraData = []): VerboseExceptionInterface;
}
