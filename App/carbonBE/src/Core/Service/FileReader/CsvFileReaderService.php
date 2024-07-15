<?php

declare(strict_types=1);

namespace Core\Service\FileReader;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CsvFileReaderService implements FileReaderInterface
{
    /**
     * Returns content of uploaded CSV file data, in a form of array of arrays.
     * Property names for each embedded array are taken from the first row of CSV file,
     * while values for those properties are taken from each sequencing row.
     *
     * Example:
     *     [
     *       [
     *           "email" => "email1@gmail.com",
     *           "first_name" => "Ema",
     *           "last_name" => "Emis",
     *           "gender" => "female",
     *           "phone_number" => "00892988333"
     *       ],
     *      [
     *          "email" => "email2@gmail.com",
     *          "first_name" => "John",
     *          "last_name" => "Merit",
     *          "gender" => "male",
     *          "phone_number" => "00892988334"
     *      ],
     *    ]
     */
    public function read(UploadedFile $file): array
    {
        $handle = fopen($file->getRealPath(), 'rb');

        if (false === $handle) {
            throw new BadRequestHttpException('Unable to read file');
        }

        $columns = [];
        $values = [];

        while (($data = fgetcsv($handle, 1000)) !== false) {
            // Columns are taken from the first row of CSV file
            if ([] === $columns) {
                $columns = $data;
                continue;
            }

            $values[] = $data;
        }

        fclose($handle);

        $data = [];
        foreach ($values as $valueSet) {
            $data[] = array_combine($columns, $valueSet);
        }

        return $data;
    }
}
