<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use ConnectionTester\ConnectionTester;

final class ConnectionTesterTest extends TestCase
{
    public function testConstructor()
    {
        $ct = new ConnectionTester();
        $this->assertInstanceOf(
            ConnectionTester::class,
            $ct
        );
        $ct->run();
    }
}
       
