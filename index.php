<?php
error_reporting(-1);
ini_set('display_errors', 'on');

require 'vendor/autoload.php';

class API extends \RestPHP\BaseAPI {

    public function example() {
        $this->setResponse($this->data);
        $this->setStatusCode(200);
    }
}

if (!isset($_REQUEST['l'])) {
    $_REQUEST['l'] = '';
}

$configuration = (new \RestPHP\Configuration)->createFromFile('config.php');

$api = new API($_REQUEST['l'], $configuration);

if (!$api->hasError()) {
    $api->getRouter()->add('/example', [$api, 'example']);
    $api->process();
}
