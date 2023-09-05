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

        $this->assertSame(['@id', 'title', 'eventDate'], $parsed->keys());
        $this->assertSame(
            ['@id', 'startDate', 'ticketing'],
            $parsed['eventDate']->keys(),
        );
    }
}
