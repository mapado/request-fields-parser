<?php declare(strict_types=1);

namespace Mapado\RequestFieldsParser;

class Parser implements ParserInterface
{
    /**
     * @var string
     */
    private $lastKey = null;

    /**
     * @var Lexer
     */
    private $lexer;

    public function __construct()
    {
        $this->lexer = new Lexer();
    }

    /**
     * parse
     */
    public function parse(string $string): array
    {
        $this->lexer->setInput($string);
        $out = $this->treatCurrent(true);

        return $out;
    }

    /**
     * return type should be recursive, but it is not handled by phpstan.
     * Next PR will use an object instead of an array so ignore this
     * @return array<string, true|array<mixed>>
     */
    private function treatCurrent(bool $isFirst): array
    {
        if ($isFirst) {
            $this->lexer->moveNext();
        }
        $this->lexer->moveNext();
        $out = [];
        while ($this->lexer->token) {
            switch ($this->lexer->token->type) {
                case Lexer::T_FIELD_NAME:
                    $out[$this->lexer->token->value] = true;
                    $this->lastKey = $this->lexer->token->value;
                    break;

                case Lexer::T_FIELD_SEPARATOR:
                    break;

                case Lexer::T_OBJECT_START:
                    $out[$this->lastKey] = $this->treatCurrent(false);
                    break;

                case Lexer::T_OBJECT_END:
                    return $out;

                default:
                    break;
            }

            $this->lexer->moveNext();
        }

        return $out;
    }
}
