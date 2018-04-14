<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use ConnectionTester\Tests\Ping;

final class PingTest extends TestCase
{
    public function testValid()
    {
        $res = (new Ping('www.google.com'))->run();

        $this->assertEquals(0, $res->getCode());
        
        $this->assertFalse(!filter_var($res->getResultData()['ip'], FILTER_VALIDATE_IP));
        $this->assertGreaterThan(0, $res->getResultData()['time']['took']);
        $this->assertGreaterThan(0, $res->getResultData()['time']['total']);
        $this->assertEquals(5, $res->getResultData()['packets']['transmitted']);
        $this->assertEquals(5, $res->getResultData()['packets']['recieved']);
        $this->assertEquals(0, $res->getResultData()['packets']['lost']);
        $this->assertGreaterThan(0, $res->getResultData()['round_trip_time']['min']);
        $this->assertGreaterThan(0, $res->getResultData()['round_trip_time']['avg']);
        $this->assertGreaterThan(0, $res->getResultData()['round_trip_time']['max']);
        $this->assertGreaterThan(0, $res->getResultData()['round_trip_time']['mdev']);

        $this->assertEquals('www.google.com resolved in ip address '.$res->getResultData()['ip'], $res->getResultMessages()[0]);
        $this->assertStringMatchesFormat('5 packets transmitted, 5 received, 0% packet loss, time %dms', $res->getResultMessages()[1]);
        $this->assertStringMatchesFormat('rtt min/avg/max/mdev = %f/%f/%f/%f', $res->getResultMessages()[2]);
        
        $this->assertEquals(['host' => 'www.google.com'], $res->getInputData());
    }
 
    public function testOptions()
    {
        $res = (new Ping('www.google.com', ['packets' => 0, 'interval' => 0.1]))->run();

        $this->assertEquals(0, $res->getCode());

        $this->assertFalse(!filter_var($res->getResultData()['ip'], FILTER_VALIDATE_IP));
        $this->assertGreaterThanOrEqual(0, $res->getResultData()['time']['took']);
        $this->assertGreaterThan(0, $res->getResultData()['time']['total']);
        $this->assertEquals(1, $res->getResultData()['packets']['transmitted']);
        $this->assertEquals(1, $res->getResultData()['packets']['recieved']);
        $this->assertEquals(0, $res->getResultData()['packets']['lost']);
        $this->assertGreaterThan(0, $res->getResultData()['round_trip_time']['min']);
        $this->assertGreaterThan(0, $res->getResultData()['round_trip_time']['avg']);
        $this->assertGreaterThan(0, $res->getResultData()['round_trip_time']['max']);
        $this->assertGreaterThanOrEqual(0, $res->getResultData()['round_trip_time']['mdev']);

        $this->assertEquals('www.google.com resolved in ip address '.$res->getResultData()['ip'], $res->getResultMessages()[0]);
        $this->assertStringMatchesFormat('1 packets transmitted, 1 received, 0% packet loss, time %dms', $res->getResultMessages()[1]);
        $this->assertStringMatchesFormat('rtt min/avg/max/mdev = %f/%f/%f/%f', $res->getResultMessages()[2]);
        
        $this->assertEquals(['host' => 'www.google.com'], $res->getInputData());
    }
    
    public function testFail()
    {
        $res = (new Ping('notavalidurl'))->run();

        $this->assertEquals(2, $res->getCode());

        $this->assertEquals($res->getResultData()['ip'], null);
        $this->assertGreaterThanOrEqual(0, $res->getResultData()['time']['took']);
        $this->assertGreaterThan(0, $res->getResultData()['time']['total']);
        $this->assertEquals(null, $res->getResultData()['packets']['transmitted']);
        $this->assertEquals(null, $res->getResultData()['packets']['recieved']);
        $this->assertEquals(null, $res->getResultData()['packets']['lost']);
        $this->assertEquals(null, $res->getResultData()['round_trip_time']['min']);
        $this->assertEquals(null, $res->getResultData()['round_trip_time']['avg']);
        $this->assertEquals(null, $res->getResultData()['round_trip_time']['max']);
        $this->assertGreaterThanOrEqual(0, $res->getResultData()['round_trip_time']['mdev']);
  
        $this->assertEquals($res->getInputData()['host'].' could not be resolved', $res->getResultMessages()[0]);

        $this->assertEquals(['host' => 'notavalidurl'], $res->getInputData());
    }
}
