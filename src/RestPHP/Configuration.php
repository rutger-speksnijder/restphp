<?php
namespace RestPHP;

/**
 * Configuration
 *
 * Configuration class for API's created with the BaseAPI class.
 *
 * @author Rutger Speksnijder
 * @since RestPHP 1.1.0
 * @package RestPHP
 * @license https://github.com/rutger-speksnijder/restphp/blob/master/LICENSE
 */
class Configuration {

	/**
	 * A value indicating whether to use authorization for this api.
	 * @var boolean
	 */
	private $useAuthorization;

	/**
	 * A value indicating what mode of authorization to use.
	 * 1: This mode will allow clients to request an access token using the "/token" endpoint.
	 * 2: This mode will allow clients to first request authorization using the "/authorize" endpoint.
	 *    This will generate an authorization code which can then be used to generate an access token.
	 * 3: Both modes can be used.
	 * @var int
	 */
	private $authorizationMode;

	/**
	 * Whether to redirect authorization requests or to just show the authorization code.
	 * @var boolean
	 */
	private $redirectAuthorization;

	/**
	 * The file to use for the authorization form.
	 * @var string
	 */
	private $authorizationForm;

	/**
	 * The data source name to use when storing OAuth2 related data.
	 * @var string
	 */
	private $dsn;

	/**
	 * The database username.
	 * @var string
	 */
	private $username;

	/**
	 * The database password.
	 * @var string
	 */
	private $password;

	/**
	 * The type of data to return.
	 * Valid types are can be defined in the Response class.
	 * Any other types will be printed as text with
	 * content-type header as text/plain.
	 * @var string
	 */
	private $returnType;

	/**
	 * A value indicating whether to allow the client to set the data return type.
	 * If this is enabled the type of data to return will be determined by the "Accept" header.
	 * Supported types can be defined in the Response class. If it's an unknown type,
	 * text/plain will be used.
	 * @var boolean
	 */
	private $clientReturnType;

	/**
	 * Construct
	 *
	 * Constructs a new instance of the Configuration class.
	 *
	 * @param boolean $useAuthorization A value indicating whether to use authorization for this api.
	 * @param int $authorizationMode A value indicating what mode of authorization to use.
	 * @param boolean $redirectAuthorization Whether to redirect authorization requests
	 * 										or to just show the authorization code.
	 * @param string $authorizationForm The file to use for the authorization form.
	 * @param string $dsn The data source name to use when storing OAuth2 related data.
	 * @param string $username The database username.
	 * @param string $password The database password.
	 * @param string $returnType The type of data to return.
	 * @param boolean $clientReturnType A value indicating whether to allow the client to set the data return type.
	 *
	 * @throws Exception Throws an exception if authorization is enabled but no data source is set.
	 *
	 * @return \RestPHP\Configuration A new instance of the Configuration class.
	 */
	public function __construct($useAuthorization, $authorizationMode,
		$redirectAuthorization, $authorizationForm, $dsn, $username,
		$password, $returnType, $clientReturnType
	) {
		if ($useAuthorization && !$dsn) {
			throw new \Exception("Authorization is enabled but no data source is set.");
		}

		$this->useAuthorization = $useAuthorization;
		$this->authorizationMode = $authorizationMode;
		$this->redirectAuthorization = $redirectAuthorization;
		$this->authorizationForm = $authorizationForm;
		$this->dsn = $dsn;
		$this->username = $username;
		$this->password = $password;
		$this->returnType = $returnType;
		$this->clientReturnType = $clientReturnType;
	}

	/**
	 * Create from file
	 *
	 * Creates a configuration object by loading the parameters from a file.
	 * This file has to be a PHP file returning an array with the parameters.
	 *
	 * @param string $file The path to the file.
	 *
	 * @throws Exception Throws an exception if the file can't be found.
	 * @throws Exception Throws an exception if the file could not be included.
	 * @throws Exception Throws an exception if the configuration object could not be created.
	 *
	 * @return \RestPHP\Configuration A new configuration object.
	 */
	public static function createFromFile($file) {
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

		// Create the configuration class
		try {
			$configuration = new \RestPHP\Configuration(
				$config['useAuthorization'],
				$config['authorizationMode'],
				$config['redirectAuthorization'],
				$config['authorizationForm'],
				$config['dsn'],
				$config['username'],
				$config['password'],
				$config['returnType'],
				$config['clientReturnType']
			);
		} catch (\Exception $ex) {
			throw $ex;
		}

		return $configuration;
	}

	/**
	 * Get use authorization
	 *
	 * Gets use authorization.
	 *
	 * @return boolean The use authorization value.
	 */
	public function getUseAuthorization() {
		return $this->useAuthorization;
	}

	/**
	 * Set use authorization
	 *
	 * Sets use authorization.
	 *
	 * @param boolean $useAuthorization The value indicating whether to use authorization.
	 *
	 * @return \RestPHP\Configuration The current object.
	 */
	public function setUseAuthorization($useAuthorization) {
		$this->useAuthorization = $useAuthorization;
		return $this;
	}

	/**
	 * Get authorization mode
	 *
	 * Gets the authorization mode.
	 *
	 * @return int The authorization mode.
	 */
	public function getAuthorizationMode() {
		return $this->authorizationMode;
	}

	/**
	 * Set authorization mode
	 *
	 * Sets the authorization mode.
	 *
	 * @param int $authorizationMode The authorization mode.
	 *
	 * @return \RestPHP\Configuration The current object.
	 */
	public function setAuthorizationMode($authorizationMode) {
		$this->authorizationMode = $authorizationMode;
		return $this;
	}

	/**
	 * Get redirect authorization
	 *
	 * Gets the redirect authorization value.
	 *
	 * @return boolean The redirect authorization value.
	 */
	public function getRedirectAuthorization() {
		return $this->redirectAuthorization;
	}

	/**
	 * Set redirect authorization
	 *
	 * Sets the redirect authorization value.
	 *
	 * @param boolean $redirectAuthorization The redirect authorization value.
	 *
	 * @return \RestPHP\Configuration The current object.
	 */
	public function setRedirectAuthorization($redirectAuthorization) {
		$this->redirectAuthorization = $redirectAuthorization;
		return $this;
	}

	/**
	 * Get authorization form
	 *
	 * Gets the authorization form value.
	 *
	 * @return string The authorization form value.
	 */
	public function getAuthorizationForm() {
		return $this->authorizationForm;
	}

	/**
	 * Set authorization form
	 *
	 * Sets the authorization form file.
	 *
	 * @param string $authorizationForm The authorization form file.
	 *
	 * @return \RestPHP\Configuration The current object.
	 */
	public function setAuthorizationForm($authorizationForm) {
		$this->authorizationForm = $authorizationForm;
		return $this;
	}

	/**
	 * Get dsn
	 *
	 * Gets the data source name.
	 *
	 * @return string The dsn.
	 */
	public function getDsn() {
		return $this->dsn;
	}

	/**
	 * Set dsn
	 *
	 * Sets the data source name.
	 *
	 * @param string $dsn The dsn.
	 *
	 * @return \RestPHP\Configuration The current object.
	 */
	public function setDsn($dsn) {
		$this->dsn = $dsn;
		return $this;
	}

	/**
	 * Get username
	 *
	 * Gets the database username.
	 *
	 * @return string The username.
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * Set username
	 *
	 * Sets the database username.
	 *
	 * @param string $username The username.
	 *
	 * @return \RestPHP\Configuration The current object.
	 */
	public function setUsername($username) {
		$this->username = $username;
		return $this;
	}

	/**
	 * Get password
	 *
	 * Gets the database password.
	 *
	 * @return string The password.
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * Set password
	 *
	 * Sets the database password.
	 *
	 * @param string $password The password.
	 *
	 * @return \RestPHP\Configuration The current object.
	 */
	public function setPassword($password) {
		$this->password = $password;
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
	 * Set return type
	 *
	 * Sets the return type.
	 *
	 * @param string $returnType The return type.
	 *
	 * @return \RestPHP\Configuration The current object.
	 */
	public function setReturnType($returnType) {
		$this->returnType = $returnType;
		return $this;
	}

	/**
	 * Get client return type
	 *
	 * Gets the client return type value.
	 *
	 * @return boolean The client return type.
	 */
	public function getClientReturnType() {
		return $this->clientReturnType;
	}

	/**
	 * Set client return type
	 *
	 * Sets the client return type value.
	 *
	 * @param boolean $clientReturnType The client return type value.
	 *
	 * @return \RestPHP\Configuration The current object.
	 */
	public function setClientReturnType($clientReturnType) {
		$this->clientReturnType = $clientReturnType;
		return $this;
	}
}
