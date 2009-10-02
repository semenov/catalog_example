<?php

require_once 'response.php';

/**
 * Response which performs a redirect.
 */
 
class Redirect implements Response
{
    protected $url;
    
    function __construct($url)
    {
        $this->url = $url;
    }
    
    public function render()
    {
        header('Location: '.$this->url);
        exit;
    }
}

?>
