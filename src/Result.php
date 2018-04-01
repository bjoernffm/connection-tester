<?php

namespace ConnectionTester;

class Result
{
    protected $code;
    protected $resultData;
    protected $resultMessages;
    protected $inputData;

    /**
     * @param int $code return code like 0=okay, 1=failed, ...
     * @param array $resultData contains all results for a programmatic use
     * @param array $resultMessages contains all result for human readable use
     * @param array $inputData contains all input data for  programmatic use
     */
    public function __construct($code, array $resultData = [], array $resultMessages = [], array $inputData = [])
    {
        $this->code = (int) $code;
        $this->resultData = $resultData;
        $this->resultMessages = $resultMessages;
        $this->inputData = $inputData;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getResultData()
    {
        return $this->resultData;
    }

    public function getResultMessages()
    {
        return $this->resultMessages;
    }

    public function getInputData()
    {
        return $this->inputData;
    }
}
