<?php

declare(strict_types=1);

namespace Mapado\RequestFieldsParser\Tests\Units;

use Mapado\RequestFieldsParser\Fields;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Fields::class)]
class FieldsTest extends TestCase
{
    public function testKeys(): void
    {
        $fields = Fields::fromArray([
            '@id' => true,
            'title' => true,
            'eventDate' => true,
        ]);
        $this->assertSame(['@id', 'title', 'eventDate'], $fields->keys());

        $fields = Fields::fromArray([
            '@id' => true,
            'title' => true,
            'eventDate' => [
                '@id' => true,
                'ticketing' => ['@id' => true, 'name' => true],
            ],
        ]);
        $this->assertSame(['@id', 'title', 'eventDate'], $fields->keys());
        $this->assertInstanceOf(Fields::class, $fields['eventDate']);
        $this->assertSame(['@id', 'ticketing'], $fields['eventDate']->keys());
    }

    public function testMerge(): void
    {
        $fields = Fields::fromArray([
            '@id' => true,
            'title' => true,
            'eventDate' => true,
        ]);
        $fields2 = Fields::fromArray([
            '@id' => true,
            'title' => true,
            'facialValue' => true,
            'eventDate' => [
                '@id' => true,
                'ticketing' => ['@id' => true, 'name' => true],
            ],
        ]);

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

        $fields = Fields::fromArray([
            '@id' => true,
            'title' => true,
            'eventDate' => ['startDate' => true],
        ]);
        $fields2 = Fields::fromArray([
            '@id' => true,
            'facialValue' => true,
            'eventDate' => [
                '@id' => true,
                'ticketing' => ['@id' => true, 'name' => true],
            ],
        ]);

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

        $fields = Fields::fromArray([
            'order' => ['ticketList' => ['valid' => true]],
        ]);

        $fields2 = Fields::fromArray([
            'order' => ['ticketList' => true],
        ]);

        $this->assertSame(
            [
                'order' => [
                    'ticketList' => [
                        'valid' => true,
                    ],
                ],
            ],
            $fields->merge($fields2)->toArray(),
        );
        $this->assertSame(
            [
                'order' => [
                    'ticketList' => [
                        'valid' => true,
                    ],
                ],
            ],
            $fields2->merge($fields)->toArray(),
        );
    }

    public function testFromArray(): void
    {
        $arrayFields = [
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
        ];

        $fields = Fields::fromArray($arrayFields);

        $this->assertSame($arrayFields, $fields->toArray());
        $this->assertInstanceOf(Fields::class, $fields);
        $this->assertInstanceOf(Fields::class, $fields['eventDate']);
        $this->assertInstanceOf(
            Fields::class,
            $fields['eventDate']['ticketing'],
        );
        $this->assertTrue($fields['eventDate']['ticketing']['name']);
    }

    /**
     * @param array<string, mixed> $fields
     */
    #[DataProvider('fromArrayExceptionProvider')]
    public function testFromArrayException(array $fields, string $message): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        // @phpstan-ignore-next-line -- check runtime and report
        Fields::fromArray($fields);
    }

    /**
     * @return iterable<array{array<string, mixed>, string}>
     */
    public static function fromArrayExceptionProvider(): iterable
    {
        yield [
            ['eventDate' => 1],
            'Invalid value for key "eventDate": array or true expected, found integer.',
        ];

        // @phpstan-ignore-next-line -- check runtime and report
        yield [
            ['eventDate'],
            'Invalid integer key "0": string expected. Maybe you wanted to use the value as key ? `eventDate => true`.',
        ];

        yield [
            ['eventDate' => ['ticketing']],
            'Invalid integer key "eventDate.0": string expected. Maybe you wanted to use the value as key ? `ticketing => true`.',
        ];

        // @phpstan-ignore-next-line -- check runtime and report
        yield [[new \stdClass()], 'Invalid integer key "0": string expected.'];

        yield [
            ['eventDate' => new \stdClass()],
            'Invalid value for key "eventDate": array or true expected, found object.',
        ];

        yield [
            ['eventDate' => ['ticketing' => ['name' => 'true']]],
            'Invalid value for key "eventDate.ticketing.name": array or true expected, found string.',
        ];
    }

    public function testToString(): void
    {
        $this->assertSame('', (string) new Fields());
        $this->assertSame('@id', (string) Fields::fromArray(['@id' => true]));
        $this->assertSame(
            '@id,name',
            (string) Fields::fromArray(['@id' => true, 'name' => true]),
        );
        $this->assertSame(
            '@id,eventDate{}',
            (string) Fields::fromArray(['@id' => true, 'eventDate' => []]),
        );

        $fields = Fields::fromArray([
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
        ]);

        $this->assertSame(
            '@id,title,eventDate{startDate,@id,ticketing{@id,name}},facialValue',
            (string) $fields,
        );
    }
}
