<?php

namespace Mapado\RequestFieldsParser;

use Doctrine\Common\Lexer\AbstractLexer as DoctrineLexer;

/**
 * Class RequestFieldLexer
 *
 * @author Julien Deniau <julien.deniau@mapado.com>
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
        return [
            '[^,\{\}]+',
            ',',
            '{',
            '}',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getNonCatchablePatterns()
    {
        return [
            '\s+',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getType(&$value)
    {
        switch ($value) {
            case ',':
                return self::T_FIELD_SEPARATOR;
                break;
            case '{':
                return self::T_OBJECT_START;
                break;
            case '}':
                return self::T_OBJECT_END;
                break;
            default:
                return self::T_FIELD_NAME;
                break;
        }
    }
}
