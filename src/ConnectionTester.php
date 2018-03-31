<?php

namespace ConnectionTester;

use GuzzleHttp\TransferStats;

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
                    'Took '.$took.' milliseconds'
                ];
            } else {
                $code = 0;
                $resultData = [
                    'ip' => $ip,
                    'took' => $took
                ];
                $resultMessages = [
                    'DNS address '.$host.' resolved to '.$ip,
                    'Took '.$took.' milliseconds'
                ];
            }

            $inputData = [
                'host' => $host
            ];

            return new TestResult($code, $resultData, $resultMessages, $inputData);
        };

        return $this;
    }

    public function testHTTP($url)
    {
        $this->tasks[] = function() use ($url) {
            $defaults = [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_FRESH_CONNECT => 1,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_SSL_VERIFYPEER => 1
            ];

            $resultData = [
                'url' => null,
                'content_type' => null,
                'http_code' => null,
                'ssl_verify_result' => null,
                'redirect_count' => null,
                'time' => [
                    'total' => null,
                    'namelookup' => null,
                    'connect' => null,
                    'pretransfer' => null,
                    'starttransfer' => null,
                    'redirect' => null
                ],
                'download_speed' => null,
                'server' => [
                    'ip' => null,
                    'port' => null
                ]
            ];

            $ch = curl_init();
            curl_setopt_array($ch, $defaults); 
            curl_exec($ch);

            
            $info = curl_getinfo($ch);

            $resultData['url'] = $info['url'];
            $resultData['content_type'] = $info['content_type'];
            $resultData['http_code'] = $info['http_code'];
            $resultData['ssl_verify_result'] = $info['ssl_verify_result'];
            $resultData['redirect_count'] = $info['redirect_count'];
            $resultData['time']['total'] = $info['total_time'];
            $resultData['time']['namelookup'] = $info['namelookup_time'];
            $resultData['time']['connect'] = $info['connect_time'];
            $resultData['time']['pretransfer'] = $info['pretransfer_time'];
            $resultData['time']['starttransfer'] = $info['starttransfer_time'];
            $resultData['time']['redirect'] = $info['redirect_time'];
            $resultData['download_speed'] = $info['speed_download'];
            $resultData['server']['ip'] = $info['primary_ip'];
            $resultData['server']['port'] = $info['primary_port'];

            // Check if any error occurred
            if (!curl_errno($ch)) {
                $code = 0;
                $resultMessages = [
                    'Request '.$url.' resulted on server '.$resultData['server']['ip'].':'.$resultData['server']['port'],
                    'Took '.$resultData['time']['total'].' in total with a download speed of '.$resultData['download_speed'],
                    'Result was a document ('.$resultData['content_type'].') with a status code '.$resultData['http_code'] 
                ];
            } else {
                $code = 1;
                $resultMessages = [
                    'Request '.$url.' resulted on server '.$resultData['server']['ip'].':'.$resultData['server']['port'],
                    'Took '.$resultData['time']['total'].' in total',
                    'Not able to get any result due to an error' 
                ];
            }            // Close handle
            curl_close($ch);
            
            $inputData = [
                'url' => $url
            ];
    
            return new TestResult($code, $resultData, $resultMessages, $inputData);
        };

        return $this;
    }
}
