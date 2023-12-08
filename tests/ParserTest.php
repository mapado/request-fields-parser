<?php

declare(strict_types=1);

namespace Mapado\RequestFieldsParser\Tests\Units;

use Mapado\RequestFieldsParser\Fields;
use Mapado\RequestFieldsParser\Parser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Parser::class)]
class ParserTest extends TestCase
{
    public function testEmpty(): void
    {
        $testedInstance = new Parser();

        $parsed = $testedInstance->parse('');

        $this->assertInstanceOf(Fields::class, $parsed);
        $this->assertSame([], $parsed->toArray());
    }


    public function testOneLevelParser(): void
    {
        $testedInstance = new Parser();

        $parsed = $testedInstance->parse('@id,title,eventDate');

        $expected = [
            '@id' => true,
            'title' => true,
            'eventDate' => true,
        ];

        $this->assertInstanceOf(Fields::class, $parsed);
        $this->assertSame($expected, $parsed->toArray());
        $this->assertTrue($parsed['@id']);
        $this->assertNull($parsed['inexistant'] ?? null);
        $this->assertSame($expected, iterator_to_array($parsed));

        foreach ($parsed as $key => $value) {
            $this->assertIsString($key);
            $this->assertIsBool($value);
        }

        $this->assertSame(['@id', 'title', 'eventDate'], $parsed->keys());
    }

    public function testMultiLevelParser(): void
    {
        $testedInstance = new Parser();

        $parsed = $testedInstance->parse(
            '@id,title,eventDate{@id,startDate,ticketing{@id}}',
        );

        $this->assertInstanceOf(Fields::class, $parsed);
        $this->assertInstanceOf(Fields::class, $parsed['eventDate']);
        $this->assertInstanceOf(
            Fields::class,
            $parsed['eventDate']['ticketing'],
        );

        $expected = [
            '@id' => true,
            'title' => true,
            'eventDate' => [
                '@id' => true,
                'startDate' => true,
                'ticketing' => [
                    '@id' => true,
                ],
            ],
        ];

        $this->assertSame($expected, $parsed->toArray());

        // instance of Fields are not converted to array in iterator
        $this->assertInstanceOf(
            Fields::class,
            iterator_to_array($parsed)['eventDate'],
        );
    }


    public function testReverseEmpty(): void
    {
        $testedInstance = new Parser();

        $reverseParsed = $testedInstance->reverseParse([]);

        $this->assertIsString($reverseParsed);
        $this->assertSame('', $reverseParsed);
    }

    public function testOneLevelReverseParser(): void
    {
        $testedInstance = new Parser();
        $reverseParsed = $testedInstance->reverseParse(['@id', 'title', 'eventDate']);
        $this->assertIsString($reverseParsed);
        $this->assertSame('@id,title,eventDate', $reverseParsed);
    }

    public function testMultiLevelReverseParser(): void
    {
        $source = [
            '@id',
            'title',
            'eventDate' => [
                '@id',
                'startDate',
                'ticketing' => [
                    '@id',
                ],
            ],
        ];

        $testedInstance = new Parser();
        $reverseParsed = $testedInstance->reverseParse($source);
        $this->assertIsString($reverseParsed);
        $this->assertSame('@id,title,eventDate{@id,startDate,ticketing{@id}}', $reverseParsed);

        $sourceWithBooleans = [
            '@id' => true,
            'title' => true,
            'eventDate' => [
                '@id' => true,
                'startDate' => true,
                'ticketing' => [
                    '@id' => true,
                ],
            ],
        ];

        $reverseParsedWithBooleans = $testedInstance->reverseParse($sourceWithBooleans);
        $this->assertIsString($reverseParsedWithBooleans);
        $this->assertSame('@id,title,eventDate{@id,startDate,ticketing{@id}}', $reverseParsedWithBooleans);
    }


    public function testReversability(): void
    {
        $testedInstance = new Parser();

        $sample = '@id,title,eventDate';
        $parsedSample = $testedInstance->parse($sample);
        $reverseParsedSample = $testedInstance->reverseParse($parsedSample);
        $this->assertSame($sample, $reverseParsedSample);

        $sample = '@id,title,eventDate{@id,startDate,ticketing{@id}}';
        $parsedSample = $testedInstance->parse($sample);
        $reverseParsedSample = $testedInstance->reverseParse($parsedSample);
        $this->assertSame($sample, $reverseParsedSample);
    }
}
