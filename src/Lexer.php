<?php declare(strict_types=1);

namespace Mapado\RequestFieldsParser;

use Doctrine\Common\Lexer\AbstractLexer as DoctrineLexer;

/**
 * @extends DoctrineLexer<int, string>
 */
class Lexer extends DoctrineLexer
{
    const T_FIELD_NAME = 1;
    const T_FIELD_SEPARATOR = 2;
    const T_OBJECT_START = 3;
    const T_OBJECT_END = 4;

    /**
     * {@inheritdoc}
     */
    protected function getCatchablePatterns()
    {
        return ['[^,\{\}]+', ',', '{', '}'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getNonCatchablePatterns()
    {
        return ['\s+'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getType(string &$value): int
    {
        switch ($value) {
            case ',':
                return self::T_FIELD_SEPARATOR;
            case '{':
                return self::T_OBJECT_START;
            case '}':
                return self::T_OBJECT_END;
            default:
                return self::T_FIELD_NAME;
        }
    }
}
