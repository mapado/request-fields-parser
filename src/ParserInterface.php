<?php declare(strict_types=1);

namespace Mapado\RequestFieldsParser;

interface ParserInterface
{
    /**
     * parse
     */
    public function parse(string $string): array;
}
