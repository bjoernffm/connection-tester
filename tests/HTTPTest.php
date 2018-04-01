<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use ConnectionTester\Tests\HTTP;

final class HTTPTest extends TestCase
{
    public function testValid()
    {
        $res = (new HTTP('https://www.google.com'))->run();

        $this->assertEquals(0, $res->getCode());

        $this->assertEquals('https://www.google.com/', $res->getResultData()['url']);
        $this->assertEquals('text/html; charset=UTF-8', $res->getResultData()['content_type']);
        $this->assertGreaterThan($res->getResultData()['http_code'], 400);
        $this->assertEquals(0, $res->getResultData()['ssl_verify_result']);
        $this->assertEquals(0, $res->getResultData()['redirect_count']);
        $this->assertGreaterThan(0, $res->getResultData()['time']['total']);
        $this->assertGreaterThan(0, $res->getResultData()['time']['namelookup']);
        $this->assertGreaterThan(0, $res->getResultData()['time']['connect']);
        $this->assertGreaterThan(0, $res->getResultData()['time']['pretransfer']);
        $this->assertGreaterThan(0, $res->getResultData()['time']['starttransfer']);
        $this->assertGreaterThanOrEqual(0, $res->getResultData()['time']['redirect']);
        $this->assertGreaterThan(0, $res->getResultData()['download_speed']);
        $this->assertFalse(!filter_var($res->getResultData()['server']['ip'], FILTER_VALIDATE_IP));
        $this->assertGreaterThan(0, $res->getResultData()['server']['port']);

        $this->assertEquals('Request https://www.google.com resulted on server '.$res->getResultData()['server']['ip'].':'.$res->getResultData()['server']['port'], $res->getResultMessages()[0]);
        $this->assertStringMatchesFormat('Took %f in total with a download speed of %d', $res->getResultMessages()[1]);
        $this->assertEquals('Result was a document ('.$res->getResultData()['content_type'].') with a status code '.$res->getResultData()['http_code'], $res->getResultMessages()[2]);
        
        $this->assertEquals(['url' => 'https://www.google.com'], $res->getInputData());
    }
    
    public function testFail()
    {
        $res = (new HTTP('notavalidurl'))->run();

        $this->assertEquals(1, $res->getCode());

        $this->assertEquals('http://notavalidurl/', strtolower($res->getResultData()['url']));
        $this->assertNull($res->getResultData()['content_type']);
        $this->assertGreaterThanOrEqual($res->getResultData()['http_code'], 400);
        $this->assertEquals(0, $res->getResultData()['ssl_verify_result']);
        $this->assertEquals(0, $res->getResultData()['redirect_count']);
        $this->assertGreaterThan(0, $res->getResultData()['time']['total']);
        $this->assertEquals(0.0, $res->getResultData()['time']['namelookup']);
        $this->assertEquals(0.0, $res->getResultData()['time']['connect']);
        $this->assertEquals(0.0, $res->getResultData()['time']['pretransfer']);
        $this->assertEquals(0.0, $res->getResultData()['time']['starttransfer']);
        $this->assertGreaterThanOrEqual(0, $res->getResultData()['time']['redirect']);
        $this->assertEquals(0.0, $res->getResultData()['download_speed']);
        $this->assertEquals('', $res->getResultData()['server']['ip']);
        $this->assertEquals(0, $res->getResultData()['server']['port']);

        $this->assertEquals('Request notavalidurl resulted in an error', $res->getResultMessages()[0]);
        $this->assertStringMatchesFormat('Took %f in total', $res->getResultMessages()[1]);
        $this->assertEquals('Not able to get any result due to an error', $res->getResultMessages()[2]);
        
        $this->assertEquals(['url' => 'notavalidurl'], $res->getInputData());
    }

}
       
