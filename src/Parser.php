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

    public function parse(string $string): Fields
    {
        $this->lexer->setInput($string);
        $out = $this->treatCurrent(true);

        return $out;
    }

    /**
     * @param iterable<string, mixed>|string $fields
     */
    public function reverseParse($fields): string
    {
        if (is_string($fields)) {
            return $fields;
        }

        $fieldsStr = '';
        foreach ($fields as $fieldName => $fieldValue) {
            if (is_array($fieldValue) || $fieldValue instanceof \Traversable) {
                $fieldsStr .= $fieldName . '{' . $this->reverseParse($fieldValue) . '},';
            } else {
                $fieldsStr .= (is_bool($fieldValue) ? $fieldName : $fieldValue) . ',';
            }
        }
        $fieldsStr = rtrim($fieldsStr, ',');

        return $fieldsStr;
    }

    private function treatCurrent(bool $isFirst): Fields
    {
        if ($isFirst) {
            $this->lexer->moveNext();
        }
        $this->lexer->moveNext();

        $out = new Fields();

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
