<?php

namespace Sunder\Dotenv;

use Sunder\Dotenv\Exceptions\CorruptedFileException;
use Sunder\Dotenv\Exceptions\UndefinedFileException;

class Parser
{
    /**
     * Array of string lines
     * @var string[]
     */
    private array $buffer;

    /**
     * Current line
     * @var string
     */
    private string $line;

    /**
     * Current line num
     * @var int
     */
    private int $lineNum = 0;

    /**
     * @throws UndefinedFileException
     */
    public function __construct(private string $filepath)
    {
        if (!file_exists($this->filepath)) {
            throw new UndefinedFileException($this->filepath);
        }
    }

    /**
     * @throws UndefinedFileException
     * @throws CorruptedFileException
     */
    public function parse(): \Generator
    {
        $this->readFile();

        $bufferSize = count($this->buffer);

        for ($this->lineNum = 0; $this->lineNum < $bufferSize; $this->lineNum++) {
            $this->extractLine();
            $this->prepareLine();

            if (!$this->lineEmpty()) {
                $this->removeCommentIfExists();
                $this->disassembleLine($key, $value);

                yield $key => $value;
            }
        }
    }

    private function extractLine(): void
    {
        $this->line = $this->buffer[$this->lineNum];
    }

    private function prepareLine(): void
    {
        $this->line = trim($this->line);
    }

    private function lineEmpty(): bool
    {
        return !strlen($this->line) || $this->line[0] === '#';
    }

    private function removeCommentIfExists(): void
    {
        if ($commentPos = $this->lineHasComment()) {
            $this->removeComment($commentPos);
        }
    }

    private function removeComment(int $commentPos): void
    {
        $this->line = rtrim(substr($this->line, 0, $commentPos));
    }

    private function lineHasComment(): int|false
    {
        return strpos($this->line, '#');
    }

    /**
     * @throws CorruptedFileException
     */
    private function disassembleLine(&$key, &$value): void
    {
        $vars = explode('=', $this->line);

        if (!isset($vars[1]) || count($vars) > 2) {
            throw new CorruptedFileException($this->filepath, $this->lineNum);
        }

        $key   = $vars[0];
        $value = $vars[1];
    }

    /**
     * @throws UndefinedFileException
     */
    private function readFile(): void
    {
        $this->buffer = explode(PHP_EOL, file_get_contents($this->filepath));
    }
}