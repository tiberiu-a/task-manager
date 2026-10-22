<?php

namespace Tests\Http;

use App\Http\JsonBodyReader;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class JsonBodyReaderTest extends TestCase
{
    private JsonBodyReader $json_body_reader;
    
    protected function setUp(): void
    {
        $this->json_body_reader = new JsonBodyReader();
    }

    public function testDecodeJsonRejectsEmptyData(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->json_body_reader->decodeJson("");
    }

    public function testDecodeJsonRejectsMalformedData(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->json_body_reader->decodeJson("abc");
    }

    public function testDecodeJsonRejectsNonArrayJson(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->json_body_reader->decodeJson('"abc"');
    }

    public function testDecodeJsonAcceptsValidData(): void
    {
        $json_string = '{"city":"Cluj","country":"RO"}';

        $decoded = $this->json_body_reader->decodeJson($json_string);

        $expected = ['city' => 'Cluj', 'country' => 'RO'];

        $this->assertSame($expected, $decoded);
    }    
}