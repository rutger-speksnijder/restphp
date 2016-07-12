<?php
namespace RestPHP;

/**
 * Base API
 *
 * Base abstract class to extend from when creating API's.
 *
 * @author Rutger Speksnijder
 * @since RestPHP 1.0
 * @version 1.0
 * @package RestPHP
 * @license https://github.com/rutger-speksnijder/restphp/blob/master/LICENSE
 */
abstract class BaseAPI {

    /**
     * The request.
     * @var string
     */
    protected $request;

    /**
     * The HTTP method this request was made in.
     * Valid methods are get, post, put or delete.
     * @var string
     */
    protected $method;

    /**
     * The data sent by the request.
     * @var array
     */
    protected $data = [];

    /**
     * The router for this api.
     * @var \RestPHP\Router
     */
    protected $router;

    /**
     * The token server for OAuth2 authorization.
     * @var \OAuth2\Server
     */
    protected $tokenServer;

    /**
     * The response from the api.
     * @var mixed
     */
    protected $response = '';

    /**
     * The return type.
     * @var string
     */
    protected $returnType;

    /**
     * The HTTP status code.
     * @var int
     */
    protected $statusCode = 200;

    /**
     * A value indicating whether the output is final.
     * Blocks the api from outputting twice.
     * @var boolean
     */
    protected $finalOutput = false;

    /**
     * An array containing configuration data.
     * @var array
     */
    protected $config;

    /**
     * An array with HTTP status messages.
     * @var array
     */
    protected $statusMessages = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
    ];

    /**
     * Construct
     *
     * Constructs a new instance of the BaseAPI class.
     * Allows for CORS, assembles and pre-processes the data.
     *
     * @param string $request The request url.
     *
     * @return BaseAPI A new instance of the BaseAPI class.
     */
    public function __construct($request) {
        // Set headers for cross domain requests
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: *');

        // Add a preceding slash if necessary
        if (substr($request, 0, 1) !== '/') {
            $request = '/' . $request;
        }

        // Set the request and the request method
        $this->request = $request;
        $this->method = strtolower($_SERVER['REQUEST_METHOD']);

        // Check for different post methods
        if ($this->method == 'post' && isset($_SERVER['HTTP_X_HTTP_METHOD'])) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'delete';
            } elseif ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->method = 'put';
            } else {
                throw new \Exception("Unexpected request method.");
            }
        }

        // Load configuration
        $this->config = require dirname(__FILE__) . '/config.php';
        $this->returnType = strtolower($this->config['returnType']);

        // Create the token server if necessary
        if ($this->config['useAuthorization']) {
            try {
                $this->tokenServer = \RestPHP\BaseAPI::createTokenServer();
            } catch (\Exception $ex) {
                throw $ex;
            }
        }

        // Sanitize request data based on the request method
        switch ($this->method) {
            case 'delete':
            case 'post':
                $this->data = $this->cleanInputs($_POST);
                break;
            case 'get':
                $this->data = $this->cleanInputs($_GET);
                break;
            case 'put':
                // Parse PUT input data
                parse_str(file_get_contents('php://input'), $this->data);
                $this->data = $this->cleanInputs($this->data);
                break;
            default:
                $this->response = 'Invalid method.';
                $this->statusCode = 405;
                $this->output(true);
                break;
        }

        // Set the return type to the client's requested return type, if any
        if ($this->config['clientReturnType'] === true && isset($this->data['return_type'])) {
            $this->returnType = strtolower($this->data['return_type']);
        }

        // Create the router
        $this->router = new \RestPHP\Router($this->method, $this->request);

        // Set our own routes
        $this->addRoutes();
    }

    /**
     * Set return type
     *
     * Sets the return type.
     *
     * @param string $returnType The return type.
     *
     * @return \RestPHP\BaseAPI The current object.
     */
    public function setReturnType($returnType) {
        $this->returnType = strtolower($returnType);
        return $this;
    }

    /**
     * Get return type
     *
     * Gets the return type.
     *
     * @return string The return type.
     */
    public function getReturnType() {
        return $this->returnType;
    }

    /**
     * Set response
     *
     * Sets the response.
     *
     * @param mixed $response The response.
     *
     * @return \RestPHP\BaseAPI The current object.
     */
    public function setResponse($response) {
        $this->response = $response;
        return $this;
    }

    /**
     * Get response
     *
     * Gets the response.
     *
     * @return mixed The response.
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * Get response as xml
     *
     * Transforms the response into an xml string.
     * This method recursively transforms the response
     * for multi-dimensional arrays.
     *
     * @param mixed $data The data to transform.
     * @param int $depth The current depth of the xml.
     *
     * @return string The xml string.
     */
    public function getResponseAsXml($data = [], $depth = 0) {
        // Set the xml string
        $xml = '';

        // Starting depth, so add the xml starting lines
        if ($depth === 0) {
            $xml = "<?xml version=\"1.0\"?>\n";
            $xml .= "<response>\n";
        }

        // Check if data is an array. If not, return a response with data to string.
        if (!is_array($data)) {
            $xml .= "<response>{$data}</response>";
            return $xml;
        }

        // Loop through the data
        foreach ($data as $k => $v) {
            // Add tabs
            for ($i = 0; $i < $depth; $i++) {
                $xml .= "\t";
            }

            // Check if the value is an array. If so, generate xml for it.
            if (is_array($v)) {
                $xml .= "<{$k}>\n{$this->getResponseAsXml($v, $depth+1)}\n</{$k}>\n";
            } else {
                $xml .= "<{$k}>{$v}</{$k}>\n";
            }
        }

        // Add the closing tag if this is depth 0
        if ($depth === 0) {
            $xml .= "</response>\n";
        }

        // Return the xml string
        return $xml;
    }

    /**
     * Get response as html
     *
     * Converts the response into an html string.
     * This is an html string with tables and underlying tables.
     *
     * @return string The response as an html string.
     */
    public function getResponseAsHtml($data = []) {
        if (!is_array($data)) {
            return "<p>{$data}</p>";
        }

        $html = "<table style=\"border: 1px solid black;\">";
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                // Transform underlying data
                $html .= "<tr><td style=\"border: 1px solid black; font-weight: bold;\">{$k}:</td><td style=\"border: 1px solid black;\">{$this->getResponseAsHtml($v)}</td></tr>";
            } else {
                $html .= "<tr><td style=\"border: 1px solid black; font-weight: bold;\">{$k}:</td><td style=\"border: 1px solid black;\">{$v}</td></tr>";
            }
        }
        $html .= "</table>";
        return $html;
    }

    /**
     * Get response as text
     *
     * Converts the response into a string.
     * If the response is an array it will loop through the array and print its values.
     *
     * @return string The response as a string.
     */
    public function getResponseAsString($data = [], $depth = 0) {
        // Return the data as string if it's not an array
        if (!is_array($data)) {
            return (string)$data;
        }

        // Loop through the data and add to the string
        $str = '';
        foreach ($data as $k => $v) {
            $str .= "\n";

            // Add tabs
            for ($i = 0; $i < $depth; $i++) {
                $str.="\t";
            }

            if (is_array($v)) {
                // Recursively transform underlying data
                $str .= "{$k}: {$this->getResponseAsString($v, $depth+1)}\n";
            } else {
                $str .= "{$k}: {$v}\n";
            }
        }
        return $str;
    }

    /**
     * Set status code
     *
     * Sets the status code.
     *
     * @param int $statusCode The status code.
     *
     * @throws Exception Throws an exception when an invalid status code is used.
     *
     * @return \RestPHP\BaseAPI The current object.
     */
    public function setStatusCode($statusCode) {
        $statusCode = (int)$statusCode;
        if ($statusCode < 100 || $statusCode >= 600) {
            throw new \Exception("Invalid status code: {$statusCode}.");
        }
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * Get status code
     *
     * Gets the status code.
     *
     * @return int The status code.
     */
    public function getStatusCode() {
        return $this->statusCode;
    }

    /**
     * Get status message
     *
     * Gets the HTTP status message for a status code.
     *
     * @param int $code The status code.
     *
     * @return string The http status.
     */
    protected function getStatusMessage($code) {
        return (isset($this->statusMessages[$code]) ? $this->statusMessages[$code] : $this->statusMessages[500]);
    }

    /**
     * Output
     *
     * Outputs the current response in the correct response type
     * and sets the headers.
     *
     * @param optional boolean $isFinal Whether this output is final or not.
     *
     * @return \RestPHP\BaseAPI The current object.
     */
    public function output($isFinal = false) {
        // Check if final output is true
        if ($this->finalOutput) {
            return;
        }

        // Disable caching
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: 0");

        // HTTP status header
        header("HTTP/1.1 {$this->statusCode} {$this->getStatusMessage($this->statusCode)}");

        $data = $this->getResponse();

        // Content type header
        if ($this->returnType == 'json') {
            header('Content-Type: application/json');
            $data = json_encode($data);
        } elseif ($this->returnType == 'xml') {
            header('Content-Type: text/xml');
            $data = $this->getResponseAsXml($data);
        } elseif ($this->returnType == 'html') {
            header('Content-Type: text/html');
            $data = $this->getResponseAsHtml($data);
        } else {
            // Unknown response type
            header('Content-Type: text/plain');
            $data = $this->getResponseAsString($data);
        }

        // Print the data and return the current object
        $this->finalOutput = $isFinal;
        echo $data;
        return $this;
    }

    /**
     * Process
     *
     * Processes the API by calling the execute method on the router.
     *
     * @return \RestPHP\BaseAPI The current object.
     */
    public function process() {
        // Check if we should verify the request
        if ($this->config['useAuthorization']) {
            if (
                $this->request != '/token' &&
                $this->request != '/authorize' &&
                !$this->tokenServer->verifyResourceRequest(\OAuth2\Request::createFromGlobals())
            ) {
                // Get the data as json and convert it to an array,
                // - so we can set our own response type.
                $data = $this->tokenServer->getResponse()->getResponseBody('json');
                $data = json_decode($data, true);

                // Set the response, status code and output content setting final output to true
                $this->response = $data;
                $this->statusCode = $this->tokenServer->getResponse()->getStatusCode();
                $this->output(true);
                return $this;
            }
        }

        try {
            // Execute the router
            $this->router->execute($this->request);
        } catch (\Exception $e) {
            // Set the response and status code to error
            $this->response = ['error' => $e->getMessage()];
            $this->statusCode = 500;
        }

        // Output the response
        $this->output();
    }

    /**
     * Clean inputs
     *
     * Cleans an array with input data.
     *
     * @param array $data The input data.
     *
     * @return array A sanitized array.
     */
    protected function cleanInputs($data) {
        $cleanInput = [];
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $cleanInput[$key] = $this->cleanInputs($value);
            }
        } else {
            $cleanInput = trim(strip_tags($data));
        }
        return $cleanInput;
    }

    /**
     * Get router
     *
     * Returns the router object.
     *
     * @return \RestPHP\Router The router object.
     */
    public function getRouter() {
        return $this->router;
    }

    /**
     * Add routes
     *
     * Adds default routes for the API regarding tokens.
     * These routes can be overridden in the child API class.
     *
     * @return \RestPHP\BaseAPI The current object.
     */
    protected function addRoutes() {
        // Define a "not found" route
        $this->router->add('', function() {
            $this->setResponse('Unknown endpoint.');
            $this->setStatusCode(404);
        });

        // Only add these routes if we're using authorization
        if ($this->config['useAuthorization']) {
            // Token route for requesting tokens
            $this->router->add('/token', array($this, 'token'));

            if ($this->config['authorizationMode'] >= 2) {
                // Token route for authorizing a client
                $this->router->add('/authorize', array($this, 'authorize'));
            }
        }

        return $this;
    }

    /**
     * Create token server
     *
     * Creates the new token server.
     * Sets the connection to the database and grant types.
     *
     * @param optional boolean $fromConfig Whether to load settings from config.
     * @param optional string $dsn The data source name.
     * @param optional string $username The database username.
     * @param optional string $password The database password.
     *
     * @throws @see OAuth2 package.
     *
     * @return \OAuth2\Server A new instance of the OAuth2 Server class.
     */
    public static function createTokenServer($fromConfig = true, $dsn = '', $username = '', $password = '') {
        // Load the configuration file
        $config = require dirname(__FILE__) . '/config.php';

        // Set variables from the configuration file
        if ($fromConfig) {
            $dsn = $config['dsn'];
            $username = $config['username'];
            $password = $config['password'];
        }

        // Catch errors for pdo object creation and creating the server
        try {
            // Create the server
            $storage = new \OAuth2\Storage\Pdo(['dsn' => $dsn, 'username' => $username, 'password' => $password]);
            $server = new \OAuth2\Server($storage);

            // Add grant types
            if ($config['authorizationMode'] === 1) {
                $server->addGrantType(new \OAuth2\GrantType\ClientCredentials($storage));
            } elseif ($config['authorizationMode'] === 2) {
                $server->addGrantType(new \OAuth2\GrantType\AuthorizationCode($storage));
            } elseif ($config['authorizationMode'] === 3) {
                $server->addGrantType(new \OAuth2\GrantType\ClientCredentials($storage));
                $server->addGrantType(new \OAuth2\GrantType\AuthorizationCode($storage));
            } else {
                throw new \Exception("Unknown authorization mode: \"{$config['authorizationMode']}\".");
            }
        } catch (\Exception $ex) {
            throw $ex;
        }

        return $server;
    }

    /**
     * Token
     *
     * Handles generating tokens for clients.
     *
     * @return mixed The result of requesting a token.
     */
    public function token() {
        // Handle the token request
        $response = $this->tokenServer->handleTokenRequest(\OAuth2\Request::createFromGlobals());

        // Get the data as json and convert it to an array,
        // - so we can set our own response type.
        $data = $response->getResponseBody('json');
        $data = json_decode($data, true);

        // Set the response, status code and output content setting final output to true
        $this->response = $data;
        $this->statusCode = $response->getStatusCode();
        $this->output(true);
        return $this;
    }

    /**
     * Authorize
     *
     * Handles authorizing clients to receive an OAuth2 access token.
     *
     * @return \RestPHP\BaseAPI The current object, unless the user gets redirected.
     */
    public function authorize() {
        // Create the request and response objects
        $request = \OAuth2\Request::createFromGlobals();
        $response = new \OAuth2\Response();

        // Validate the authorize request
        if (!$this->tokenServer->validateAuthorizeRequest($request, $response)) {
            // Get the data as json and convert it to an array,
            // - so we can set our own response type.
            $data = $response->getResponseBody('json');
            $data = json_decode($data, true);

            // Set the response, status code and output content setting final output to true
            $this->response = $data;
            $this->statusCode = $response->getStatusCode();
            $this->output(true);
            return $this;
        }

        // Display an authorization form
        if ($this->method != 'post' || !isset($this->data['authorized'])) {
            // Get the authorization form
            ob_start();
            require dirname(__FILE__) . '/form.php';
            $form = ob_get_clean();
            $this->response = $form;
            $this->statusCode = 200;
            $this->returnType = 'html';
            $this->output(true);
            return $this;
        }

        // Print the authorization code if the user has authorized your client
        $is_authorized = ($this->data['authorized'] === 'yes');
        $this->tokenServer->handleAuthorizeRequest($request, $response, $is_authorized);

        // Check if the request was successful
        if ($response->getStatusCode() === 302) {
            // Check if we should redirect according to our configuration
            if ($this->config['redirectAuthorization'] === true) {
                // The response will contain a Location header we should navigate to
                header('Location: ' . $response->getHttpHeader('Location'));
                exit;
            }

            // Create a response with the authorization code
            $code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=')+5, 40);
            $this->response = array('status' => 1, 'authorization_code' => $code);
            $this->statusCode = 200;
            $this->output(true);
            return $this;
        }

        // Get the data as json and convert it to an array,
        // - so we can set our own response type.
        $data = $response->getResponseBody('json');
        $data = json_decode($data, true);

        // Set the response, status code and output content setting final output to true
        $this->response = $data;
        $this->statusCode = $response->getStatusCode();
        $this->output(true);
        return $this;
    }
}