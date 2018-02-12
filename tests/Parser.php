<?php

namespace Mapado\RequestFieldsParser\Tests\Units;

use atoum;

class Parser extends atoum
{
    public function testOneLevelParser()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->array($parsed = $this->testedInstance->parse('@id,title,eventDate'))
                ->isEqualTo([
                    '@id' => true,
                    'title' => true,
                    'eventDate' => true,
                ])

        ;
    }

    public function testMultiLevelParser()
    {
        $this
            ->given($this->newTestedInstance)
            ->then
                ->array($parsed = $this->testedInstance->parse('@id,title,eventDate{@id,startDate,ticketing{@id}}'))
                ->isEqualTo([
                    '@id' => true,
                    'title' => true,
                    'eventDate' => [
                        '@id' => true,
                        'startDate' => true,
                        'ticketing' => [
                            '@id' => true,
                        ],
                    ]
                ])

        ;
    }
}
