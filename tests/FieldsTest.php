<?php

declare(strict_types=1);

namespace Mapado\RequestFieldsParser\Tests\Units;

use Mapado\RequestFieldsParser\Fields;
use Mapado\RequestFieldsParser\Parser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Fields::class)]
class FieldsTest extends TestCase
{
    public function testKeys(): void
    {
        $parser = new Parser();

        $fields = $parser->parse('@id,title,eventDate');
        $this->assertSame(['@id', 'title', 'eventDate'], $fields->keys());

        $fields = $parser->parse(
            '@id,title,eventDate{@id,ticketing{@id,name}}',
        );
        $this->assertSame(['@id', 'title', 'eventDate'], $fields->keys());
        $this->assertSame(['@id', 'ticketing'], $fields['eventDate']->keys());
    }

    public function testMerge(): void
    {
        $parser = new Parser();

        $fields = $parser->parse('@id,title,eventDate');
        $fields2 = $parser->parse(
            '@id,facialValue,eventDate{@id,ticketing{@id,name}}',
        );

        $merged = $fields->merge($fields2);

        $this->assertNotSame($fields, $merged);
        $this->assertSame(
            [
                '@id' => true,
                'title' => true,
                'eventDate' => [
                    '@id' => true,
                    'ticketing' => [
                        '@id' => true,
                        'name' => true,
                    ],
                ],
                'facialValue' => true,
            ],
            $merged->toArray(),
        );

        $fields = $parser->parse('@id,title,eventDate{startDate}');
        $fields2 = $parser->parse(
            '@id,facialValue,eventDate{@id,ticketing{@id,name}}',
        );

        $merged = $fields->merge($fields2);
        $this->assertSame(
            [
                '@id' => true,
                'title' => true,
                'eventDate' => [
                    'startDate' => true,
                    '@id' => true,
                    'ticketing' => [
                        '@id' => true,
                        'name' => true,
                    ],
                ],
                'facialValue' => true,
            ],
            $merged->toArray(),
        );
    }
}
