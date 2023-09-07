<?php declare(strict_types=1);

namespace Mapado\RequestFieldsParser;

interface ParserInterface
{
    public function parse(string $string): Fields;
}
