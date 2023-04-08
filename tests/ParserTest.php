<?php

class ParserTest extends \PHPUnit\Framework\TestCase
{
    const ENV_FILEPATH = __DIR__ . '/src/.env';

    private \Sunder\Dotenv\Parser $parser;

    public function __construct(string $name)
    {
        parent::__construct($name);

        $this->parser = new \Sunder\Dotenv\Parser(self::ENV_FILEPATH);
    }

    public function test_corrupted_file_exception()
    {
        $parser = new \Sunder\Dotenv\Parser(self::ENV_FILEPATH . '.corrupted');
        try {
            foreach ($parser->parse() as $value);
        } catch (\Sunder\Dotenv\Exceptions\CorruptedFileException) {
            $this->assertTrue(true);
        }
    }

    public function test_undefined_file_exception()
    {
        try {
            new \Sunder\Dotenv\Parser(self::ENV_FILEPATH . 'some_string');
            $this->fail();
        } catch (\Sunder\Dotenv\Exceptions\UndefinedFileException) {
            $this->assertTrue(true);
        }
    }

    public function test_parse()
    {
        $expectedArray = [
            'VAR_FOO' => 'foo',
            'VAR_BAR' => 'bar',
            'VAR_INT' => '123',
            'VAR_BOOL_TRUE' => 'true',
            'VAR_BOOL_FALSE' => 'false'
        ];

        $outputArray = [];
        foreach ($this->parser->parse() as $key => $value) {
            $outputArray[$key] = $value;
        }

        $this->assertEquals($outputArray, $expectedArray);
    }
}