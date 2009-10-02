<?php

require_once 'response.php';

/** 
 * A class for the view rendering.
 */   

class View implements Response
{
    /** 
     * Constructs the view.
     * @param $template a path to the template file.
     * @param $data an array containing template data.
     */  
    function __construct($template, $data)
    {
        $this->template = $template;
        $this->data = $data;
    }
    
    /** 
     * Renders the view.
     */   
        
    public function render()
    {
        extract($this->data);
        
        ob_start();
        include("views/{$this->template}.phtml");
        $page_content = ob_get_contents();
        ob_end_clean();
        
        include("views/layout.phtml");
    }
}

?>
