<?php declare(strict_types=1);

namespace Mapado\RequestFieldsParser;

use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * @implements ArrayAccess<string, true|Fields>
 * @implements IteratorAggregate<string, true|array<mixed>>
 */
class Fields implements ArrayAccess, IteratorAggregate
{
    /** @var array<string, true|Fields> */
    private $fields = [];

    /** @return array<string> */
    public function keys(): array
    {
        return array_keys($this->fields);
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

    /** @return array<string, true|array<mixed>> */
    public function toArray(): array
    {
        return array_map(
            fn($value) => $value instanceof Fields ? $value->toArray() : $value,
            $this->fields,
        );
    }
}
