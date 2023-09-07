<?php declare(strict_types=1);

namespace Mapado\RequestFieldsParser;

use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use Stringable;
use Traversable;

/**
 * @implements ArrayAccess<string, true|Fields>
 * @implements IteratorAggregate<string, true|array<mixed>>
 */
class Fields implements ArrayAccess, IteratorAggregate, Stringable
{
    /** @var array<string, true|Fields> */
    private $fields = [];

    /** @return array<string> */
    public function keys(): array
    {
        return array_keys($this->fields);
    }

    /**
     * Convert an array to a Fields. reverse function of `toArray`
     *
     * @param array<string, true|array<mixed>> $arrayFields
     */
    public static function fromArray(
        array $arrayFields,
        string $previousKey = '',
    ): Fields {
        $fields = new self();

        foreach ($arrayFields as $key => $value) {
            $nextKey = $previousKey ? "{$previousKey}.{$key}" : $key;

            if (is_array($value)) {
                // @phpstan-ignore-next-line -- issue with recursive call not handled by phpstan
                $fields[$key] = self::fromArray($value, $nextKey);
            } elseif ($value === true) {
                $fields[$key] = true;
                // @phpstan-ignore-next-line -- check runtime and report
            } elseif (is_int($key)) {
                if (is_string($value)) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Invalid integer key "%s": string expected. Maybe you wanted to use the value as key ? `%s => true`.',
                            $nextKey,
                            $value,
                        ),
                    );
                    // @phpstan-ignore-next-line -- check runtime and report
                } else {
                    throw new \InvalidArgumentException(
                        sprintf(
                            'Invalid integer key "%s": string expected.',
                            $nextKey,
                        ),
                    );
                }
            } else {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Invalid value for key "%s": array or true expected, found %s.',
                        $nextKey,
                        gettype($value),
                    ),
                );
            }
        }

        return $fields;
    }

    /**
     * Convert the fields instance to an array. You can convert back to a Fields instance by calling `Mapado\RequestFieldsParser\Fields::fromArray`
     *
     * @return array<string, true|array<mixed>>
     */
    public function toArray(): array
    {
        return array_map(
            fn($value) => $value instanceof Fields ? $value->toArray() : $value,
            $this->fields,
        );
    }

    public function merge(Fields $newFields): Fields
    {
        $fields = clone $this;

        foreach ($newFields as $key => $value) {
            if (
                isset($fields[$key]) &&
                $fields[$key] instanceof Fields &&
                $value instanceof Fields
            ) {
                $fields[$key] = $fields[$key]->merge($value);
            } else {
                $fields[$key] = $value;
            }
        }

        return $fields;
    }

    /** @return ArrayIterator<string, true|Fields> */
    public function getIterator(): Traversable
    {
        $iterator = new ArrayIterator($this->fields);

        // @phpstan-ignore-next-line -- isue with true that is converted to bool
        return $iterator;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->fields[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->fields[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->fields[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->fields[$offset]);
    }

    public function __toString(): string
    {
        $result = '';

        foreach ($this as $key => $value) {
            if ($value instanceof Fields) {
                $result .= $key . '{' . $value . '},';
            } else {
                $result .= $key . ',';
            }
        }

        $result = trim($result, ',');

        return $result;
    }
}
