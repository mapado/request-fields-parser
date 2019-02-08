# request-fields-parser

Convert string like `id,firstname,lastname,jobs{startDate,position,company{id,recordNumber}}` to the following array:
```php
[
    'id' => true,
    'firstname' => true,
    'lastname' => true,
    'jobs' => [
        'startDate' => true,
        'position' => true,
        'company' => [
            'id' => true,
            'recordNumber' => true,
        ],
    ]
]
```

You can think of it like an [explode](https://php.net/explode) on steroids.

## Installation

```sh
composer require mapado/request-fields-parser
```


## Usage

```php
use Mapado\RequestFieldsParser\Parser;

$parser = new Parser();

$outArray = $parser->parse($string);
```

## Extensibility

You can decorate the Parser like this:

```php
use Mapado\RequestFieldsParser\ParserInterface;

class ExtendedParser implements ParserInterface
{
    /**
     * @var ParserInterface
     */
    private $decoratedParser;

    public function __construct(ParserInterface $decoratedParser)
    {
        $this->decoratedParser = $decoratedParser;
    }

    public function parse(string $sttring): array
    {
        // do stuff and return an array
    }
}
```
