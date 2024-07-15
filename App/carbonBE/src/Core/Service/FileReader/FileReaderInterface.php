<?php

declare(strict_types=1);

namespace Core\Service\FileReader;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface FileReaderInterface
{
    public function read(UploadedFile $file): mixed;
}
