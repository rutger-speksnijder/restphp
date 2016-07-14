<?php
/**
 * Config
 *
 * Default configuration file for the RestPHP class.
 * Make sure to define your data source.
 *
 * @author Rutger Speksnijder
 * @since RestPHP 1.0.0
 * @package RestPHP
 * @license https://github.com/rutger-speksnijder/restphp/blob/master/LICENSE
 */
return [
	// Whether to use authorization for this api
	'useAuthorization' => true,

	// The authorization mode to use.
	// - 1: This mode will allow clients to request an access token using the "/token" endpoint.
	// - 2: This mode will allow clients to first request authorization using the "/authorize" endpoint.
	// - 	This will generate an authorization code which can then be used to generate an access token.
	// - 3: Both modes can be used.
	'authorizationMode' => 2,

	// Whether to redirect authorization requests or to just show the authorization code
	'redirectAuthorization' => false,

	// The authorization form file. This file will be loaded when the client
	// - navigates to the "/authorize" endpoint. You can overwrite this setting
	// - and show another form, as long as you keep the authorized input with values "yes/no".
	'authorizationForm' => dirname(__FILE__) . '/form.php',

	// Data source name for your application
	'dsn' => 'mysql:dbname=YOUR_DATABASE_NAME;host=localhost',

	// Database username
	'username' => '',

	// Database password
	'password' => '',

	// The return type for the api.
	// - Valid types can be defined in the Response class.
	// - Any other types will be printed as text with
	// - content-type header as text/plain.
	'returnType' => 'json',

	// Whether the return type can be set by the client using the "Accept" header
	'clientReturnType' => true,
];
