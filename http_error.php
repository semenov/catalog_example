<?php

/**
 * An exception to generate an HTTP error. 
 */
 
class HttpError extends Exception
{
    const NOT_FOUND = '404 Not Found';
    const FORBIDDEN = '403 Forbidden';
    
    function __construct($error_code)
    {
        $this->error_code = $error_code;
    }
    
    public function respond()
    {
        header('HTTP/1.1 '.$this->error_code);
        die($this->error_code);
    }
}

?>
