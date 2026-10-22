<?php

namespace App\Http;

use InvalidArgumentException;

class JsonBodyReader
{
    public function decodeJson(string $string_data): array
    {
        $data = json_decode($string_data, true);

        if (!is_array($data)) {
            throw new InvalidArgumentException("Invalid data format");
        }

        return $data;
    }
}