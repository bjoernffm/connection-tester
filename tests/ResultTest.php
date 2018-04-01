<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use ConnectionTester\Result;

final class TestResultTest extends TestCase
{
    public function testConstructor()
    {
        $result = new Result(0, ['a' => 1], ['message'], ['key' => 'value']);

        $this->assertEquals(0, $result->getCode());
        $this->assertEquals(['a' => 1], $result->getResultData());
        $this->assertEquals(['message'], $result->getResultMessages());
        $this->assertEquals(['key' => 'value'], $result->getInputData());
    }

    public function testConstructorFail()
    {
        $result = new Result('a', ['a' => 1], ['message'], ['key' => 'value']);

        $this->assertEquals(0, $result->getCode());
        $this->assertEquals(['a' => 1], $result->getResultData());
        $this->assertEquals(['message'], $result->getResultMessages());
        $this->assertEquals(['key' => 'value'], $result->getInputData());
    }

}
       
