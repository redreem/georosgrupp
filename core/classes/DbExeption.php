<?php

class DbException extends Exception
{
    /**
     * @param string $e
     */
    function __construct($e)
    {
        parent::__construct($e);
    }
}