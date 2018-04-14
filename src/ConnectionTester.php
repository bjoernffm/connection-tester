<?php

namespace ConnectionTester;

use ConnectionTester\Tests\TestInterface;

class ConnectionTester
{
    protected $tests = [];

    public function with(TestInterface $test)
    {
        $this->tests[] = $test;

        return $this;
    }

    public function run()
    {
        $results = [];

        foreach ($this->tests as $test) {
            $results[] = $test->run();
        }

        return $results;
    }
}
