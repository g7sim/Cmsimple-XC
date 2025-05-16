<?php

namespace Plib;

use PHPUnit\Framework\TestCase;

class CodecTest extends TestCase
{
    /** @dataProvider dataForEncodeBase64url*/
    public function testEncodeBase64url(string $string, string $expected): void
    {
        $this->assertSame($expected, Codec::encodeBase64url($string));
    }

    public function dataForEncodeBase64url(): array
    {
        return [
            ["Many hands make light work.", "TWFueSBoYW5kcyBtYWtlIGxpZ2h0IHdvcmsu"],
            [hex2bin("fb315671cd6127f4fef46e86ba28ef"), "-zFWcc1hJ_T-9G6Guijv"],
            ["a", "YQ"],
            ["ab", "YWI"],
        ];
    }

    /** @dataProvider dataForDecodeBase64url */
    public function testDecodeBase64url(string $string, ?string $expected): void
    {
        $this->assertSame($expected, Codec::decodeBase64url($string));
    }

    public function dataForDecodeBase64url(): array
    {
        return [
            ["TWFueSBoYW5kcyBtYWtlIGxpZ2h0IHdvcmsu", "Many hands make light work."],
            ["-zFWcc1hJ_T-9G6Guijv", hex2bin("fb315671cd6127f4fef46e86ba28ef")],
            ["YQ", "a"],
            ["YWI", "ab"],
            ["\\", null],
        ];
    }

    /** @dataProvider encodeBase32hexData */
    public function testEncodeBase32hex(string $input, string $expected): void
    {
        $actual = Codec::encodeBase32hex($input);
        $this->assertEquals($expected, $actual);
    }

    public function encodeBase32hexData(): array
    {
        return [
            ["", ""],
            ["f", "CO======"],
            ["fo", "CPNG===="],
            ["foo", "CPNMU==="],
            ["foob", "CPNMUOG="],
            ["fooba", "CPNMUOJ1"],
            ["foobar", "CPNMUOJ1E8======"],
        ];
    }

    /** @dataProvider decodeBase32hexData */
    public function testDecodeBase32hex(string $input, string $expected): void
    {
        $actual = Codec::decodeBase32hex($input);
        $this->assertEquals($expected, $actual);
    }

    public function decodeBase32hexData(): array
    {
        return [
            ["", ""],
            ["CO======", "f"],
            ["CPNG====", "fo"],
            ["CPNMU===", "foo"],
            ["CPNMUOG=", "foob"],
            ["CPNMUOJ1", "fooba"],
            ["CPNMUOJ1E8======", "foobar"],
        ];
    }
}
