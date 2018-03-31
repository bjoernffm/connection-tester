<?php

namespace ConnectionTester;

abstract class AbstractConnectionTester
{
    protected $tasks = [];

    public function run()
    {
        $results = [];

        foreach($this->tasks as $task) {
            $results[] = $task();
        }

        return $results;
    }
}
