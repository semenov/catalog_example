<?php

/**
 * Parses url, calls appropriate action and renders the view.
 */

require_once 'config.php';
require_once 'database.php';
require_once 'view.php';
require_once 'redirect.php';
require_once 'http_error.php';
require_once 'application.php';

$action = (isset($_GET['action']) ? $_GET['action'] : 'index');

$application = new Application();

try 
{
    if (!method_exists($application, $action))
    {
        throw new HttpError(HttpError::NOT_FOUND);
    }

    $application->$action()->render();
}
catch (HttpError $exception) 
{
    $exception->respond();
}
    
?>
