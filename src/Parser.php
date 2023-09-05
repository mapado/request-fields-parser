<?php declare(strict_types=1);

namespace Mapado\RequestFieldsParser;

// use Mapado\RequestFieldsParser\DataCollector\StopwatchTrait;

/**
 * Class parser
 *
 * @author Julien Deniau <julien.deniau@mapado.com>
 */
class Parser implements ParserInterface
{
    // use StopwatchTrait;

    /**
     * lastKey
     *
     * @var string
     */
    private $lastKey = null;

    /**
     * lexer
     *
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
     * treatCurrent
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
