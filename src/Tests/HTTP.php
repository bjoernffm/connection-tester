<?php

namespace ConnectionTester\Tests;

use ConnectionTester\Result;

class HTTP implements TestInterface
{
    protected $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function run()
    {
        $defaults = [
            CURLOPT_URL => $this->url,
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

        if (!curl_errno($ch)) {
            $code = 0;
            $resultMessages = [
                'Request '.$this->url.' resulted on server '.$resultData['server']['ip'].':'.$resultData['server']['port'],
                'Took '.$resultData['time']['total'].' in total with a download speed of '.$resultData['download_speed'],
                'Result was a document ('.$resultData['content_type'].') with a status code '.$resultData['http_code']
            ];
        } else {
            $code = 1;
            $resultMessages = [
                'Request '.$this->url.' resulted in an error',
                'Took '.$resultData['time']['total'].' in total',
                'Not able to get any result due to an error'
            ];
        }            // Close handle
        curl_close($ch);
            
        $inputData = [
            'url' => $this->url
        ];
    
        return new Result($code, $resultData, $resultMessages, $inputData);
    }
}
