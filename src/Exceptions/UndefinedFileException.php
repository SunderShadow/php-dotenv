<?php

namespace Sunder\Dotenv\Exceptions;

class UndefinedFileException extends \Exception implements DotenvException
{
    public function __construct(string $filename)
    {
        parent::__construct("Undefined file: \"$filename\"");
    }
}