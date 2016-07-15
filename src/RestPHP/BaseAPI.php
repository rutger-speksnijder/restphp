<?php
namespace RestPHP;

/**
 * Base API
 *
 * Base abstract class to extend from when creating API's.
 *
 * @author Rutger Speksnijder
 * @since RestPHP 1.0.0
 * @package RestPHP
 * @license https://github.com/rutger-speksnijder/restphp/blob/master/LICENSE
 */
abstract class BaseAPI {

    /**
     * The request uri.
     * @var string
     */
    protected $uri;

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
     * The response from the api.
     * @var mixed
     */
    protected $response = '';

    /**
     * The hypertext routes the client can follow
     * after the current request.
     * @var array
     */
    protected $hypertextRoutes = array();

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
     * A value indicating whether the output is final.
     * Blocks the api from outputting twice.
     * @var boolean
     */
    protected $finalOutput = false;

    /**
     * The configuration object for this api.
     * @var \RestPHP\Configuration
     */
    protected $configuration;

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
     * @param string $uri The request uri.
     * @param optional object $configuration The configuration object.
     *          If this is false, the default config.php file will be used.
     *
     * @throws Exception Throws an exception for unknown request methods.
     * @throws Exception Throws an exception if the token server can't be created.
     * @throws Exception Throws an exception if configuration is
     * supplied but is not an instance of \RestPHP\Configuration.
     * @throws Exception Throws an exception if the configuration
     * object can't be created from the default file.
     *
     * @return BaseAPI A new instance of the BaseAPI class.
     */
    public function __construct($uri, $configuration = false) {
        // Set headers for cross domain requests
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: *');

        // Add a preceding slash if necessary
        if (substr($uri, 0, 1) !== '/') {
            $uri = '/' . $uri;
        }

        // Set the request uri and the request method
        $this->uri = $uri;
        $this->method = strtolower($_SERVER['REQUEST_METHOD']);

        // Check for different post methods
        if ($this->method == 'post' && isset($_SERVER['HTTP_X_HTTP_METHOD'])) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'delete';
            } elseif ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->method = 'put';
            }
        }

        // Check if we have a configuration object
        if ($configuration !== false) {
            if (!($configuration instanceof \RestPHP\Configuration)) {
                throw new \Exception("Supplied configuration object is not an instance of \\RestPHP\\Configuration.");
            }
            $this->configuration = $configuration;
        } else {
            // Load from default configuration
            try {
                $this->configuration = \RestPHP\Configuration::createFromFile(dirname(__FILE__) . '/config.php');
            } catch (\Exception $ex) {
                throw $ex;
            }
        }

        // Set the return type
        if ($this->configuration->getClientReturnType() === true) {
            $this->returnType = strtolower(\RestPHP\Response::parseAcceptHeader());
        } else {
            $this->returnType = strtolower($this->configuration->getReturnType());
        }

        // Create the token server if necessary
        if ($this->configuration->getUseAuthorization()) {
            try {
                $this->tokenServer = \RestPHP\BaseAPI::createTokenServer($this->configuration);
            } catch (\Exception $ex) {
                throw $ex;
            }
        }

        // Create the request object and get the request data
        $request = \RestPHP\Request::createRequest();
        $this->data = $request->getData();

        // Return an error message on invalid method
        if (!in_array($this->method, ['delete', 'post', 'get', 'put', 'patch', 'head', 'options'])) {
            $this->response = 'Invalid method.';
            $this->statusCode = 405;
            $this->output(true);
            return;
        }

        // Create the router
        $this->router = new \RestPHP\Router($this->method, $this->uri);

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
     * Get configuration
     *
     * Gets the configuration object.
     *
     * @return \RestPHP\Configuration The configuration object.
     */
    public function getConfiguration() {
        return $this->configuration;
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
     * Get hypertext routes
     *
     * Gets the hypertext routes.
     *
     * @return array The hypertext routes.
     */
    public function getHypertextRoutes() {
        return $this->hypertextRoutes;
    }

    /**
     * Set hypertext routes
     *
     * Sets the hypertext routes.
     *
     * @param array $hypertextRoutes The array with hypertext routes.
     *
     * @return \RestPHP\BaseAPI The current object.
     */
    public function setHypertextRoutes($hypertextRoutes) {
        $this->hypertextRoutes = $hypertextRoutes;
        return $this;
    }

    /**
     * Remove hypertext route
     *
     * Removes a hypertext route by name.
     *
     * @param string $name The hypertext route's name.
     *
     * @return \RestPHP\BaseAPI The current object.
     */
    public function removeHypertextRoute($name) {
        if (isset($this->hypertextRoutes[$name])) {
            unset($this->hypertextRoutes[$name]);
        }
        return $this;
    }

    /**
     * Add hypertext route
     *
     * Adds a hypertext route.
     *
     * @param string $name The hypertext route's name.
     * @param string $route The route.
     *
     * @return \RestPHP\BaseAPI The current object.
     */
    public function addHypertextRoute($name, $route) {
        $this->hypertextRoutes[$name] = $route;
        return $this;
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
        $this->finalOutput = $isFinal;

        // Disable caching
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: 0");

        // HTTP status header
        header("HTTP/1.1 {$this->statusCode} {$this->getStatusMessage($this->statusCode)}");

        // Add the current uri to the hypertext routes
        $this->hypertextRoutes['self'] = $this->uri;

        // Create the response object, output the headers and print the response
        $response = \RestPHP\Response::createResponse($this->returnType, $this->response, $this->hypertextRoutes);
        $response->outputHeaders();
        echo $response;

        // Return the current object
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
        if ($this->configuration->getUseAuthorization()) {
            if (
                $this->uri != '/token' &&
                $this->uri != '/authorize' &&
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
            // Handle OPTIONS requests
            if ($this->method == 'options') {
                $this->handleOptionsRequest();
            } else {
                // Execute the router
                $this->router->execute();
            }
        } catch (\Exception $e) {
            // Set the response and status code to error
            $this->response = ['error' => $e->getMessage()];
            $this->statusCode = 500;
        }

        // Output the response
        $this->output();
    }

    /**
     * Handle options request
     *
     * Handles an HTTP OPTIONS requests.
     * Returns available methods for the current route.
     *
     * @return null.
     */
    public function handleOptionsRequest() {
        // Generate the "Allow" header
        $allow = '';
        foreach ($this->router->getMethodsByRoute($this->uri) as $method) {
            $allow .= strtoupper($method) . ',';
        }
        $allow = substr($allow, 0, strlen($allow) - 1);
        header('Allow: ' . $allow);
        $this->setResponse('');
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
            $this->setResponse(array('error' => 1, 'message' => 'Unknown endpoint.'));
            $this->setStatusCode(404);
        });

        // Only add these routes if we're using authorization
        if ($this->configuration->getUseAuthorization()) {
            // Token route for requesting tokens
            $this->router->add('/token', array($this, 'token'));

            if ($this->configuration->getAuthorizationMode() >= 2) {
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
     * @param optional object $configuration Whether to load
     *  settings from a configuration object.
     * @param optional string $dsn The data source name.
     * @param optional string $username The database username.
     * @param optional string $password The database password.
     *
     * @throws @see OAuth2 package.
     *
     * @return \OAuth2\Server A new instance of the OAuth2 Server class.
     */
    public static function createTokenServer($configuration = false, $dsn = '', $username = '', $password = '') {
        // Set variables from the configuration object
        if ($configuration && ($configuration instanceof \RestPHP\Configuration)) {
            $dsn = $configuration->getDsn();
            $username = $configuration->getUsername();
            $password = $configuration->getPassword();
        }

        // Catch errors for pdo object creation and creating the server
        try {
            // Create the server
            $storage = new \OAuth2\Storage\Pdo(['dsn' => $dsn, 'username' => $username, 'password' => $password]);
            $server = new \OAuth2\Server($storage);

            // Add grant types
            if ($configuration->getAuthorizationMode() === 1) {
                $server->addGrantType(new \OAuth2\GrantType\ClientCredentials($storage));
            } elseif ($configuration->getAuthorizationMode() === 2) {
                $server->addGrantType(new \OAuth2\GrantType\AuthorizationCode($storage));
            } elseif ($configuration->getAuthorizationMode() === 3) {
                $server->addGrantType(new \OAuth2\GrantType\ClientCredentials($storage));
                $server->addGrantType(new \OAuth2\GrantType\AuthorizationCode($storage));
            } else {
                throw new \Exception("Unknown authorization mode: \"{$configuration->getAuthorizationMode()}\".");
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
            require $this->configuration->getAuthorizationForm();
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
            if ($this->configuration->getRedirectAuthorization() === true) {
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
