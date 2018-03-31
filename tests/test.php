<?php 

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use ConnectionTester\ConnectionTester;

$res = (new ConnectionTester())
        ->testDNSResolution('www.google.de')
        ->testDNSResolution('api.expalas.io')
        ->run();

var_dump($res);
