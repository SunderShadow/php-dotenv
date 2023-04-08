<?php

namespace Sunder\Dotenv;

use Sunder\Dotenv\Exceptions\CorruptedFileException;
use Sunder\Dotenv\Exceptions\UndefinedFileException;

class Dotenv implements \ArrayAccess
{
    private array $data = [];

    /**
     * @throws CorruptedFileException
     * @throws UndefinedFileException
     */
    public function load(string $filepath): void
    {
        $parser = new Parser($filepath);

        foreach ($parser->parse() as $key => $value)
        {
            $this->data[$key] = $this->castValue($value);
        }
    }

    private function castValue(string $value): bool|string
    {
        return match ($value) {
            'true'  => true,
            'false' => false,
            default => $value
        };
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function offsetExists(mixed $offset): bool
    {
        return key_exists($offset, $this->data);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->data[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->data[$offset]);
    }
}