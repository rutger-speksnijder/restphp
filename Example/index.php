<?php
// Enable errors
error_reporting(-1);
ini_set('display_errors', 'On');

// Autoloader
require 'vendor/autoload.php';

// Check for an empty request
if (!isset($_REQUEST['l'])) {
    $_REQUEST['l'] = '';
}

// Your API class extending from the BaseAPI
class API extends \RestPHP\BaseAPI
{
    public function example()
    {
        $this->response = ['message' => 'This is an example message!'];
        $this->statusCode = 200;
    }

    public function user($id)
    {
        if ($this->method == 'get') {
            $this->response = ['message' => 'You requested user with id: ' . $id . '.'];
            $this->statusCode = 200;
            $this->addHypertextRoute('connect', "/user/{$id}/connect");
            $this->addHypertextRoute('disconnect', "/user/{$id}/disconnect");
        } elseif ($this->method == 'head') {
            // Code to check if the user with this id exists
            $found = false;
            $this->statusCode = 200;
            if (!$found) {
                $this->statusCode = 404;
            }
        }
    }
}

// Create the API using the default configuration file
$api = new API($_REQUEST['l']);

// Check if no errors occurred during creation
// If errors did occur they must be fixed before the API will work.
// The errors are most likely OAuth2 related.
if (!$api->hasError()) {
    // Define routes
    $api->getRouter()->get('/example', [$api, 'example']);
    $api->getRouter()->get('/user/([0-9]+)', [$api, 'user']);
    $api->getRouter()->head('/user/([0-9]+)', [$api, 'user']);

    // Call the process method
    $api->process();
}
