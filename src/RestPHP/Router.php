<?php
namespace RestPHP;

/**
 * Router
 *
 * Router class for routing routes to api methods.
 *
 * @author Rutger Speksnijder
 * @since RestPHP 1.0.0
 * @version 1.0.0
 * @package RestPHP
 * @license https://github.com/rutger-speksnijder/restphp/blob/master/LICENSE
 */
class Router {

	/**
	 * The array with routes and their callables and arguments.
	 * @var array
	 */
	private $routes = [
		'any' => [],
		'get' => [],
		'post' => [],
		'put' => [],
		'delete' => [],
	];

	/**
	 * The request method.
	 * @var string
	 */
	private $method = 'get';

	/**
	 * The request url.
	 * @var string
	 */
	private $url;

	/**
	 * Construct
	 *
	 * Constructs a new instance of the Router object.
	 *
	 * @param string $method The request method.
	 * @param string $url The request url.
	 *
	 * @return \RestPHP\Router The new object.
	 */
	public function __construct($method = 'get', $url = '') {
		$this->method = $method;
		$this->url = $url;
	}

	/**
	 * Set method
	 *
	 * Sets the method.
	 *
	 * @param string $method The method.
	 *
	 * @return \RestPHP\Router The current object.
	 */
	public function setMethod($method) {
		$this->method = strtolower($method);
		return $this;
	}

	/**
	 * Get method
	 *
	 * Gets the request method.
	 *
	 * @return string The request method.
	 */
	public function getMethod() {
		return $this->method;
	}

	/**
	 * Set url
	 *
	 * Sets the request url.
	 *
	 * @param string $url The url.
	 *
	 * @return \RestPHP\Router The current object.
	 */
	public function setUrl($url) {
		$this->url = $url;
		return $this;
	}

	/**
	 * Get url
	 *
	 * Gets the request url.
	 *
	 * @return string The url.
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * Add
	 *
	 * Adds a route to the router.
	 *
	 * @param string $route The route.
	 * @param callable $callable The method to execute when this route gets called.
	 * @param string $type The request type to bind this route to.
	 *
	 * @return \RestPHP\Router The current object.
	 */
	public function add($route, $callable, $type = 'any') {
		$this->routes[strtolower($type)][$route] = $callable;
		return $this;
	}

	/**
	 * Get
	 *
	 * Adds a route to the router using type "get".
	 *
	 * @param @see \RestPHP\Router::add.
	 *
	 * @return @see \RestPHP\Router::add.
	 */
	public function get($route, $callable) {
		return $this->add($route, $callable, 'get');
	}

	/**
	 * Post
	 *
	 * Adds a route to the router using type "post".
	 *
	 * @param @see \RestPHP\Router::add.
	 *
	 * @return @see \RestPHP\Router::add.
	 */
	public function post($route, $callable) {
		return $this->add($route, $callable, 'post');
	}

	/**
	 * Put
	 *
	 * Adds a route to the router using type "put".
	 *
	 * @param @see \RestPHP\Router::add.
	 *
	 * @return @see \RestPHP\Router::add.
	 */
	public function put($route, $callable) {
		return $this->add($route, $callable, 'put');
	}

	/**
	 * Delete
	 *
	 * Adds a route to the router using type "delete".
	 *
	 * @param @see \RestPHP\Router::add.
	 *
	 * @return @see \RestPHP\Router::add.
	 */
	public function delete($route, $callable) {
		return $this->add($route, $callable, 'delete');
	}

	/**
	 * Remove
	 *
	 * Removes a route.
	 *
	 * @param string $route The route to remove.
	 * @param string $type The request type.
	 *
	 * @return \RestPHP\Router The current object.
	 */
	public function remove($route, $type = 'any') {
		if (isset($this->routes[$type][$route])) {
			unset($this->routes[$type][$route]);
		}
		return $this;
	}

	/**
	 * Execute
	 *
	 * Executes the router based on the url.
	 *
	 * @throws Exception Throws an exception if no location was set and no default route was found.
	 *
	 * @return mixed The result of the callable method.
	 */
	public function execute() {
		// Check if we have a url
		if (!$this->url || trim($this->url) == '') {
			$this->url = '';
		}

		// Check if the absolute route exists in our routes array
		// - and if that route has the same type of request as the current request.
		if (isset($this->routes[$this->method][$this->url])) {
			return $this->routes[$this->method][$this->url]();
		}

		// Check if the absolute route exists in our routes array
		// - and if that route has the "any" type.
		if (isset($this->routes['any'][$this->url])) {
			return $this->routes['any'][$this->url]();
		}

		// Create an array of methods to check and loop through them
		$methods = array($this->method, 'any');
		foreach ($methods as $method) {
			// Loop through the routes set for this method
			foreach ($this->routes[$method] as $route => $callable) {
				// Check if the route matches
				$regex = '/^' . str_replace('/', '\/', $route) . '$/im';
				$matches = array();
				if (preg_match($regex, $this->url, $matches) === 1) {
					// First value in the array is the string that matched
					array_shift($matches);

					// Execute the callable method
					return call_user_func_array($callable, $matches);
				}
			}
		}

		// No route found, check if we have an empty route.
		// - We should always have an empty route.
		if (!isset($this->routes['any'][''])) {
			// No empty route found, throw an exception
			throw new \Exception("No \"not found\" route set.");
		}

		// Return the empty route's callable
		return $this->routes['any']['']();
	}
}
