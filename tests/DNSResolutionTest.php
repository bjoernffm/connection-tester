<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use ConnectionTester\Tests\DNSResolution;

final class DNSResolutionTest extends TestCase
{
    public function testValid()
    {
        $res = (new DNSResolution('www.google.com'))->run();

        $this->assertEquals(0, $res->getCode());
        $this->assertFalse(!filter_var($res->getResultData()['ip'], FILTER_VALIDATE_IP));
        $this->assertGreaterThan(0, $res->getResultData()['took']);
        $this->assertStringStartsWith('DNS address www.google.com resolved to', $res->getResultMessages()[0]);
        $this->assertStringMatchesFormat('Took %f milliseconds', $res->getResultMessages()[1]);
        $this->assertEquals(['host' => 'www.google.com'], $res->getInputData());
    }

    public function testFail()
    {
        $res = (new DNSResolution('thisdoesnotexist'))->run();
 
        $this->assertEquals(1, $res->getCode());
        $this->assertNull($res->getResultData()['ip']);
        $this->assertGreaterThan(0, $res->getResultData()['took']);
        $this->assertEquals('DNS address thisdoesnotexist could not be resolved', $res->getResultMessages()[0]);
        $this->assertStringMatchesFormat('Took %f milliseconds', $res->getResultMessages()[1]);
        $this->assertEquals(['host' => 'thisdoesnotexist'], $res->getInputData());
    }
}
