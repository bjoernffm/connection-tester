<?php

namespace ConnectionTester\Tests;

use ConnectionTester\Result;

class DNSResolution implements TestInterface
{
    protected $host;

    public function __construct($host)
    {
        $this->host = $host;
    }

    public function run()
    {
        $start = microtime(true);
        $ip = gethostbyname($this->host);
        $took = microtime(true)-$start;

        if ($ip == $this->host) {
            $code = 1;
            $resultData = [
                'ip' => null,
                'took' => $took
            ];
            $resultMessages = [
                'DNS address '.$this->host.' could not be resolved',
                'Took '.$took.' milliseconds'
            ];
        } else {
            $code = 0;
            $resultData = [
                'ip' => $ip,
                'took' => $took
            ];
            $resultMessages = [
                'DNS address '.$this->host.' resolved to '.$ip,
                'Took '.$took.' milliseconds'
            ];
        }

        $inputData = [
            'host' => $this->host
        ];

        return new Result($code, $resultData, $resultMessages, $inputData);
    }
}
