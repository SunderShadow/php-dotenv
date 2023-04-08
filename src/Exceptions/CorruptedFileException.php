<?php

namespace Sunder\Dotenv\Exceptions;

class CorruptedFileException extends \Exception implements DotenvException
{
    public function __construct(string $filename, int $line)
    {
        parent::__construct("Corrupted file on line $line: \"$filename\"");
    }
}