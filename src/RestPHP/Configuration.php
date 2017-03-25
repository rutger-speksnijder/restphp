<?php
namespace RestPHP;

/**
 * Configuration class for API's created with the BaseAPI class.
 *
 * @author Rutger Speksnijder.
 * @since RestPHP 1.0.
 * @license https://github.com/rutger-speksnijder/restphp/blob/master/LICENSE MIT.
 */
class Configuration
{
    /**
     * A value indicating whether to use authorization for this api.
     * @var boolean.
     */
    private $useAuthorization;

    /**
     * A value indicating what mode of authorization to use.
     * 1: This mode will allow clients to request an access token using the "/token" endpoint.
     * 2: This mode will allow clients to first request authorization using the "/authorize" endpoint.
     *    This will generate an authorization code which can then be used to generate an access token.
     * 3: Both modes can be used.
     * @var int.
     */
    private $authorizationMode;

    /**
     * Whether to redirect authorization requests or to just show the authorization code.
     * @var boolean.
     */
    private $redirectAuthorization;

    /**
     * The file to use for the authorization form.
     * This file will be loaded when the client
     * navigates to the "/authorize" endpoint. You can overwrite this setting
     * and show another form, as long as you keep the authorized input with values "yes/no".
     * @var string.
     */
    private $authorizationForm;

    /**
     * The data source name to use when storing OAuth2 related data.
     * @var string.
     */
    private $dsn;

    /**
     * The database username.
     * @var string.
     */
    private $username;

    /**
     * The database password.
     * @var string.
     */
    private $password;

    /**
     * The type of response data.
     * Valid types are can be defined in the Response factory.
     * If the client can set the the response type, the value of this
     * setting will be used as a fallback.
     * @var string.
     */
    private $responseType;

    /**
     * A value indicating whether to allow the client to set the data response type.
     * If this is enabled the type of response data will be determined by the "Accept" header.
     * Supported types can be defined in the Response factory.
     * If the client provides an unsupported type, the "responseType" setting's value
     * will be used as a fallback.
     * @var boolean.
     */
    private $clientResponseType;

    /**
     * Constructs a new instance of the Configuration class.
     *
     * @param boolean $useAuthorization A value indicating whether to use authorization for this api.
     * @param int $authorizationMode A value indicating what mode of authorization to use.
     * @param boolean $redirectAuthorization Whether to redirect authorization requests
     *                                      or to just show the authorization code.
     * @param string $authorizationForm The file to use for the authorization form.
     * @param string $dsn The data source name to use when storing OAuth2 related data.
     * @param string $username The database username.
     * @param string $password The database password.
     * @param string $responseType The type of response data.
     * @param boolean $clientResponseType A value indicating whether to allow the client to set the response type.
     */
    public function __construct($useAuthorization = null, $authorizationMode = null,
        $redirectAuthorization = null, $authorizationForm = null,
        $dsn = null, $username = null, $password = null,
        $responseType = null, $clientResponseType = null
    )
    {
        $this->useAuthorization = $useAuthorization;
        $this->authorizationMode = $authorizationMode;
        $this->redirectAuthorization = $redirectAuthorization;
        $this->authorizationForm = $authorizationForm;
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->responseType = $responseType;
        $this->clientResponseType = $clientResponseType;
    }

    /**
     * Creates a configuration object by loading the parameters from a file.
     * This file has to be a PHP file returning an array with the parameters.
     *
     * @param string $file The path to the file.
     *
     * @throws Exception Throws an exception if the file can't be found.
     * @throws Exception Throws an exception if the file could not be included.
     *
     * @return $this The current object.
     */
    public function createFromFile($file)
    {
        // Check if the file exists
        if (!$file || !file_exists($file)) {
            throw new \Exception("Configuration file \"{$file}\" could not be found.");
        }

        // Include the file while suppressing warnings
        // - so we can check if we could include it
        $config = @include($file);
        if (!$config) {
            throw new \Exception("Configuration file \"{$file}\" could not be included.");
        }

        // Set the properties
        $this->useAuthorization = $config['useAuthorization'];
        $this->authorizationMode = $config['authorizationMode'];
        $this->redirectAuthorization = $config['redirectAuthorization'];
        $this->authorizationForm = $config['authorizationForm'];
        $this->dsn = $config['dsn'];
        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->responseType = $config['responseType'];
        $this->clientResponseType = $config['clientResponseType'];
        return $this;
    }

    /**
     * Gets use authorization.
     *
     * @return boolean The use authorization value.
     */
    public function getUseAuthorization()
    {
        return $this->useAuthorization;
    }

    /**
     * Sets use authorization.
     *
     * @param boolean $useAuthorization The value indicating whether to use authorization.
     *
     * @return $this The current object.
     */
    public function setUseAuthorization($useAuthorization)
    {
        $this->useAuthorization = $useAuthorization;
        return $this;
    }

    /**
     * Gets the authorization mode.
     *
     * @return int The authorization mode.
     */
    public function getAuthorizationMode()
    {
        return $this->authorizationMode;
    }

    /**
     * Sets the authorization mode.
     *
     * @param int $authorizationMode The authorization mode.
     *
     * @return $this The current object.
     */
    public function setAuthorizationMode($authorizationMode)
    {
        $this->authorizationMode = $authorizationMode;
        return $this;
    }

    /**
     * Gets the redirect authorization value.
     *
     * @return boolean The redirect authorization value.
     */
    public function getRedirectAuthorization()
    {
        return $this->redirectAuthorization;
    }

    /**
     * Sets the redirect authorization value.
     *
     * @param boolean $redirectAuthorization The redirect authorization value.
     *
     * @return $this The current object.
     */
    public function setRedirectAuthorization($redirectAuthorization)
    {
        $this->redirectAuthorization = $redirectAuthorization;
        return $this;
    }

    /**
     * Gets the authorization form value.
     *
     * @return string The authorization form value.
     */
    public function getAuthorizationForm()
    {
        return $this->authorizationForm;
    }

    /**
     * Sets the authorization form file.
     *
     * @param string $authorizationForm The authorization form file.
     *
     * @return $this The current object.
     */
    public function setAuthorizationForm($authorizationForm)
    {
        $this->authorizationForm = $authorizationForm;
        return $this;
    }

    /**
     * Gets the data source name.
     *
     * @return string The dsn.
     */
    public function getDsn()
    {
        return $this->dsn;
    }

    /**
     * Sets the data source name.
     *
     * @param string $dsn The dsn.
     *
     * @return $this The current object.
     */
    public function setDsn($dsn)
    {
        $this->dsn = $dsn;
        return $this;
    }

    /**
     * Gets the database username.
     *
     * @return string The username.
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Sets the database username.
     *
     * @param string $username The username.
     *
     * @return $this The current object.
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Gets the database password.
     *
     * @return string The password.
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Sets the database password.
     *
     * @param string $password The password.
     *
     * @return $this The current object.
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Gets the response type.
     *
     * @return string The response type.
     */
    public function getResponseType()
    {
        return $this->responseType;
    }

    /**
     * Sets the response type.
     *
     * @param string $responseType The response type.
     *
     * @return $this The current object.
     */
    public function setReturnType($responseType)
    {
        $this->responseType = $responseType;
        return $this;
    }

    /**
     * Gets the client response type value.
     *
     * @return boolean The client response type.
     */
    public function getClientResponseType()
    {
        return $this->clientResponseType;
    }

    /**
     * Sets the client response type value.
     *
     * @param boolean $clientResponseType The client response type value.
     *
     * @return $this The current object.
     */
    public function setClientResponseType($clientResponseType)
    {
        $this->clientResponseType = $clientResponseType;
        return $this;
    }
}
