# RestPHP
[![Latest Stable Version](https://poser.pugx.org/rutger/restphp/v/stable)](https://packagist.org/packages/rutger/restphp)
[![Total Downloads](https://poser.pugx.org/rutger/restphp/downloads)](https://packagist.org/packages/rutger/restphp)
[![License](https://poser.pugx.org/rutger/restphp/license)](https://packagist.org/packages/rutger/restphp)
[![Build Status](https://travis-ci.org/rutger-speksnijder/restphp.svg?branch=master)](https://travis-ci.org/rutger-speksnijder/restphp)

RestPHP is a very basic PHP package for creating RESTful API's. It supports OAuth2 authentication using this package: http://bshaffer.github.io/oauth2-server-php-docs/.

The RestPHP package contains a base class from which can be extended to create API's. The package contains a simple Router class for setting routes for different HTTP methods.

## Security
The package can be configured to use OAuth2 to secure your API. The configuration parameters for this are explained in the "config.php" configuration file. The package used for creating and managing access tokens is located here: http://bshaffer.github.io/oauth2-server-php-docs/. You can google "OAuth2" to get more familiar with how it works.
Security can also be turned off if you want to create your own method of authorization, or don't want to secure your API at all.

The base API class uses the same endpoints as described in the tutorial for the package mentioned above (http://bshaffer.github.io/oauth2-server-php-docs/cookbook/), except without the ".php" suffix.

## Supported content types
RestPHP can return data in multiple content types. The following types are supported:
 - JSON
 - XML
 - HTML
 - Plain text

You can add support for other content types by editing the array in the Response class and adding a class for your content type.
More on that below under usage.

You can also let the client decide which type of content should be returned.
The "Accept" header will be parsed and the content type will be returned according to the header's value.
This can also be turned off to only allow for the content type set in the configuration to be returned.

## Supported HTTP methods
RestPHP supports the following HTTP methods:
 - GET
 - POST
 - PUT
 - DELETE
 - HEAD
 - OPTIONS
 - PATCH

## Installation
Install using composer (this will install the latest stable version):
```sh
composer require rutger/restphp
```
If you want to get the most recent development version:
```sh
composer require rutger/restphp dev-master
```
Or you can download the package and create an autoloader for it (below is an example of how to do this).

## Configuration
Before you start using RestPHP change the default config.php file located in the package's folder.
You can also copy this file and place it in another location to create separate configurations for separate API's.

If you want to use OAuth2 to secure your API, you will have to configure these settings:
 - useAuthorization: Set this to true to use authorization
 - authorizationMode
 - redirectAuthorization
 - authorizationForm
 - dsn
 - username
 - password

Remaining settings that should be configured before you start using RestPHP:
 - returnType: The content type of data to return.
 - clientReturnType: Whether to allow the client to specify the "return_type" parameter.

More information about these settings can be found in the default config.php file.

## Usage
You probably want to create an htaccess file which points all requests to a PHP file in which you create your API class.

### htaccess
An example of an htaccess file which points all requests to an index.php file:
```
RewriteEngine on
RewriteBase /
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?l=$1 [L,QSA,NC]
```

### index.php
In your index.php file you would then create your API class.
An example of this:
```php
<?php
// Enable errors
error_reporting(-1);
ini_set('display_errors', 'On');

// Autoloader example if you're not using composer
spl_autoload_register(function($className) {
    $className = ltrim($className, '\\');
    $fileName  = '';
    $namespace = '';
    if ($lastNsPos = strrpos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

    require $fileName;
});

// Composer:
// require 'vendor/autoload.php';

// Check for an empty request
if (!isset($_REQUEST['l'])) {
    $_REQUEST['l'] = '';
}

// Your API class extending from the BaseAPI
class API extends \RestPHP\BaseAPI {
    public function example() {
        $this->setResponse(array('message' => 'This is an example message!'));
        $this->setStatusCode(200);
    }

    public function user($id) {
        if ($this->method == 'get') {
            $this->setResponse(array('message' => 'You requested user with id: ' . $id . '.'));
            $this->setStatusCode(200);
            $this->addHypertextRoute('connect', "/user/{$id}/connect");
            $this->addHypertextRoute('disconnect', "/user/{$id}/disconnect");
        } elseif ($this->method == 'head') {
            // Code to check if the user with this id exists
            $found = false;
            if ($found) {
                $this->setStatusCode(200);
            } else {
                $this->setStatusCode(404);
            }
        }
    }
}

// Create the API using the default configuration file
$api = new API($_REQUEST['l']);

// Define routes
$api->getRouter()->get('/example', array($api, 'example'));
$api->getRouter()->get('/user/([0-9]+)', array($api, 'user'));
$api->getRouter()->head('/user/([0-9]+)', array($api, 'user'));

// Call the process method
$api->process();
```
In the example above we created three routes, one to "/example" and two to "/user/(any number here)".

The route to "/example" will return a simple message.
The route to "/user/(any number here)" will return a message if the request is a GET request.
If the request to "/user/(any number here)" is a HEAD request, we will check if the user exists and set the status code accordingly.

HEAD requests can be used to quickly check if a resource exists on a server, without retrieve a response body (http://www.pragmaticapi.com/blog/2013/02/14/restful-patterns-for-the-head-verb).

#### Examples of using a configuration object
Using a configuration file:
```php
<?php
// Create the API using a different configuration file
$configuration = \RestPHP\Configuration::createFromFile('config.php');
$api = new API($_REQUEST['l'], $configuration);
```
Creating the object:
```php
<?php
// Create the API by creating our own configuration object
$configuration = new \RestPHP\Configuration(
    $useAuthorization = false,
    $authorizationMode = 1,
    $redirectAuthorization = false,
    $authorizationForm = $_SERVER['DOCUMENT_ROOT'] . '/myform.php',
    $dsn = false,
    $username = false,
    $password = false,
    $returnType = 'html',
    $clientReturnType = true
);
$api = new API($_REQUEST['l'], $configuration);
```
#### Adding a response content type
Edit the $supportedTypes static array and the $supportedAcceptHeaders static array in the Response class:
```php
public static $supportedTypes = array(
    // Other types...
    // ...
    // Adding a new response type
    'image' => '\\RestPHP\\ResponseTypes\\ImageResponse',
);

public static $supportedAcceptHeaders = array(
    // Add any accept headers that should point to the new response type
    'image/jpeg' => 'image',
    'image/png' => 'image',
);
```

Create a response class in the ResponseTypes folder:
```php
namespace RestPHP\ResponseTypes;

class ImageResponse extends \RestPHP\Response {
    // Default response string
    protected $response = '';

    // Headers to output when sending this response
    protected $headers = array(
        'Content-Type: image/png',
    );

    // The main method that gets called to transform the data
    protected function transform($data, $hypertextRoutes = array()) {
        return $this->transformToImage($data, $hypertextRoutes);
    }

    // Logic to transform an array or string of data into an image
    //
    // Make sure to also transform the hypertext routes into the appropriate format
    // An example of this can be found in the other response type classes
    private function transformToImage($data, $hypertextRoutes = array()) {
        return array_merge($data, $hypertextRoutes);
    }
}
```

And that's all there is to it. You can now set "image" as your response type in the configuration,
or let clients specify the "Accept" header and return an image based on that.

### HATEOAS
The RestPHP library supports the HATEOAS constraint.
In the example above, in the "user" method, we added two hypertext routes.
Hypertext routes basically tell the client what can be done next after a request (http://restcookbook.com/Basics/hateoas/).
In the example we added a connect and disconnect hypertext route to the response (currently these routes don't do anything).
The response type classes SHOULD take care of these extra routes and SHOULD add them to the response.
A link with the current route and relationship "self" is always added to the hypertext routes.

See http://restcookbook.com/Basics/hateoas/ for more information about this constraint.

### Routes
The router object allows you to create routes to your methods. It supports the use of regex and has the following methods:
 - $api->getRouter()->get(): Only execute this method if the request is a GET request.
 - $api->getRouter()->post(): Only execute this method if the request is a POST request.
 - $api->getRouter()->put(): Only execute this method if the request is a PUT request.
 - $api->getRouter()->delete(): Only execute this method if the request is a DELETE request.
 - $api->getRouter()->head(): Only execute this method if the request is a HEAD request.
 - $api->getRouter()->options(): Only execute this method if the request is an OPTIONS request*.
 - $api->getRouter()->patch(): Only execute this method if the request is a PATCH request.
 - $api->getRouter()->add(): Execute this method for any type of request.

\* By default, you don't have to specify methods for an OPTIONS request. The BaseAPI class will handle these requests and look up all available methods for the requested route. You can disable this by searching in the BaseAPI class for "this->method == 'options'".

## Todo
 - Add support for accepting different content types (e.g. let clients perform requests with xml or json)
 - Add support for caching
 - Explain security a bit more
 - Extend unit tests and add code coverage

## Contact
If you find any bugs, you can create an issue on the issues tab.
If you have any questions feel free to contact me at rutgerspeksnijder@hotmail.com.

## License
The MIT License (MIT)

Copyright (c) 2016 Rutger Speksnijder

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
