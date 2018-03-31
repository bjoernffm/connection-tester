<?php

namespace ConnectionTester;

class ConnectionTester extends AbstractConnectionTester
{
    public function testDNSResolution($host)
    {
        $this->tasks[] = function() use ($host) {
            $start = microtime(true);
            $ip = gethostbyname($host);
            $took = microtime(true)-$start;

            if ($ip == $host) {
                $code = 1;
                $resultData = [
                    'ip' => null,
                    'took' => $took
                ];
                $resultMessages = [
                    'DNS address '.$host.' could not be resolved',
                    'Took '.$ip.' milliseconds'
                ];
            } else {
                $code = 0;
                $resultData = [
                    'ip' => $ip,
                    'took' => $took
                ];
                $resultMessages = [
                    'DNS address '.$host.' resolved to '.$ip,
                    'Took '.$ip.' milliseconds'
                ];
            }

            $inputData = [
                'host' => $host
            ];

            return new TestResult($code, $resultData, $resultMessages, $inputData);
        };

        return $this;
    }
}
