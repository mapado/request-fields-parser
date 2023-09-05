<?php declare(strict_types=1);

namespace Mapado\RequestFieldsParser;

interface ParserInterface
{
    /**
     * return type should be recursive, but it is not handled by phpstan.
     * Next PR will use an object instead of an array so ignore this
     * @return array<string, true|array<mixed>>
     */
    public function parse(string $string): array;
}
