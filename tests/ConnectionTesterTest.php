<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use ConnectionTester\ConnectionTester;
use ConnectionTester\Tests\DNSResolution;

final class ConnectionTesterTest extends TestCase
{
    public function testWith()
    {
        $res = (new ConnectionTester())
                ->with(new DNSResolution('www.google.de'))
                ->with(new DNSResolution('doesnotexist'))
                ->run();

        $this->assertEquals(0, $res[0]->getCode());
        $this->assertEquals(1, $res[1]->getCode());
    }

    public function testRun()
    {
        $res = (new ConnectionTester())->run();

        $this->assertEquals([], $res);
    }
}
