<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use ConnectionTester\ConnectionTester;

final class DNSResolutionTest extends TestCase
{
    public function testValid()
    {
        $res = (new ConnectionTester())
                ->testDNSResolution('www.google.com')
                ->run();

        $this->assertEquals(1, count($res));
        $res = $res[0];
        
        $this->assertEquals(0, $res->getCode());

        $this->assertFalse(!filter_var($res->getResultData()['ip'], FILTER_VALIDATE_IP));
        $this->assertGreaterThan(0, $res->getResultData()['took']);

        $this->assertStringStartsWith('DNS address www.google.com resolved to', $res->getResultMessages()[0]);
        $this->assertStringMatchesFormat('Took %f milliseconds', $res->getResultMessages()[1]);

        $this->assertEquals(['host' => 'www.google.com'], $res->getInputData());
    }

    public function testFail()
    {
        $res = (new ConnectionTester())
                ->testDNSResolution('thisdoesnotexist')
                ->run();

        $this->assertEquals(1, count($res));
        $res = $res[0];
        
        $this->assertEquals(1, $res->getCode());

        $this->assertNull($res->getResultData()['ip']);
        $this->assertGreaterThan(0, $res->getResultData()['took']);

        $this->assertEquals('DNS address thisdoesnotexist could not be resolved', $res->getResultMessages()[0]);
        $this->assertStringMatchesFormat('Took %f milliseconds', $res->getResultMessages()[1]);

        $this->assertEquals(['host' => 'thisdoesnotexist'], $res->getInputData());
    }
 
    public function testMultiple()
    {
        $res = (new ConnectionTester())
                ->testDNSResolution('www.google.com')
                ->testDNSResolution('thisdoesnotexist')
                ->run();

        $this->assertEquals(2, count($res));

        $this->assertEquals(0, $res[0]->getCode());       
        $this->assertEquals(1, $res[1]->getCode());
    }
}
       
