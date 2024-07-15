<?php

declare(strict_types=1);

namespace Core\Service;

use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Validation;

final class UrlHelper
{
    /**
     * Validates external URL and appends any given parameters to its query part.
     *
     * @param array $queryParameters Parameter list in associative array form, e.g ['param' => 'joker']
     *
     * @return string Absolute URL
     */
    public static function external(string $url, array $queryParameters = []): string
    {
        self::ensureValidUrl($url);

        if ([] !== $queryParameters) {
            return sprintf(
                '%s%s%s',
                $url,
                str_contains($url, '?') ? '&' : '?',
                http_build_query($queryParameters)
            );
        }

        return $url;
    }

    public static function validateUrl(string $url): void
    {
        self::ensureValidUrl($url);
    }

    /**
     * Validates URL using the Symfony validator URL constraint.
     */
    private static function ensureValidUrl(string $url): void
    {
        $violations = Validation::createValidator()->validate($url, new Url());

        if (0 !== count($violations)) {
            throw new \InvalidArgumentException(sprintf('"%s" is not a valid URL.', $url));
        }
    }
}
