<?php

namespace ConnectionTester\Tests;

use ConnectionTester\Result;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Ping implements TestInterface
{
    protected $host;
    protected $options;

    public function __construct($host, array $options = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'packets' => 5,
            'interval' => 0.2
        ]);

        $this->options = $resolver->resolve($options);
        $this->host = $host;

        $this->options['packets'] = (int) $this->options['packets'];
        if($this->options['packets'] <= 0) {
            $this->options['packets'] = 1;
        }

        $this->options['interval'] = (double) $this->options['interval'];
        if ($this->options['interval'] < 0.2) {
            $this->options['interval'] = 0.2;
        }
    }

    public function run()
    {
        $output = [];
        $resultData = [
            'ip' => null,
            'time' => [
                'took' => null,
                'total' => null
            ],
            'packets' => [
                'transmitted' => null,
                'recieved' => null,
                'lost' => null
            ],
            'round_trip_time' => [
                'min' => null,
                'avg' => null,
                'max' => null,
                'mdev' => null
            ]
        ];
        $resultMessages = [];

        $start = microtime(true);
        exec('ping -q -c '.$this->options['packets'].' -i '.$this->options['interval'].' '.$this->host, $output, $code);
        $resultData['time']['total'] = (int) round((microtime(true)-$start)*1000);

        preg_match('#PING [\S]+ \(([\d\.]+)#', $output[0], $results);
        if (count($results) == 2) {
            $resultData['ip'] = $results[1];
            $resultMessages[] = $this->host.' resolved in ip address '.$resultData['ip'];
        } else {
            $resultMessages[] = $this->host.' could not be resolved';
        }

        preg_match('#(\d+) packets transmitted, (\d+) received, \d+% packet loss, time (\d+)ms#', $output[count($output)-2], $results);
        if(count($results) == 4) {
            $resultData['time']['took'] = (int) $results[3];
            $resultData['packets'] = [
                'transmitted' => (int) $results[1],
                'recieved' => (int) $results[2],
                'lost' => (int) $results[1]-$results[2]
            ];
            $resultMessages[] = $results[0];
        } else {
            $resultMessages[] = 'No packet statistic available';
        }

        preg_match('#rtt min/avg/max/mdev = (\d+[\.\d]*)/(\d+[\.\d]*)/(\d+[\.\d]*)/(\d+[\.\d]*)#', $output[count($output)-1], $results);
        if (count($results) == 5) {
            $resultData['round_trip_time'] = [
                'min' => (double) $results[1],
                'avg' => (double) $results[2],
                'max' => (double) $results[3],
                'mdev' => (double) $results[4]
            ];
            $resultMessages[] = $results[0];
        } else {
            $resultMessages[] = 'No rtt information available';
        }
        
        $inputData = [
            'host' => $this->host
        ];

        return new Result($code, $resultData, $resultMessages, $inputData);
    }
}
 
