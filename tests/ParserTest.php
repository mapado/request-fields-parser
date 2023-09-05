<?php

declare(strict_types=1);

namespace Mapado\RequestFieldsParser\Tests\Units;

use Mapado\RequestFieldsParser\Parser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Parser::class)]
class ParserTest extends TestCase
{
    public function testOneLevelParser(): void
    {
        $testedInstance = new Parser();

        $parsed = $testedInstance->parse('@id,title,eventDate');

        $this->assertSame(
            [
                '@id' => true,
                'title' => true,
                'eventDate' => true,
            ],
            $parsed,
        );
    }

    public function testMultiLevelParser(): void
    {
        $testedInstance = new Parser();

        $parsed = $testedInstance->parse(
            '@id,title,eventDate{@id,startDate,ticketing{@id}}',
        );
        $this->assertSame(
            [
                '@id' => true,
                'title' => true,
                'eventDate' => [
                    '@id' => true,
                    'startDate' => true,
                    'ticketing' => [
                        '@id' => true,
                    ],
                ],
            ],
            $parsed,
        );
    }
}
