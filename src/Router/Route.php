<?php

namespace Mvc\Router;

use Mvc\Http\Request;
use Mvc\View\View;

class Route {
	/**
	 * Route container
	 *
	 * @var array $routes
	 */
	private static $routes = [];

	/**
	 * Middleware
	 *
	 * @var string $middleware
	 */
	private static $middleware;

	/**
	 * Prefix
	 *
	 * @var string $prefix
	 */
	private static $prefix;

	/**
	 * Route constructor
	 *
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Add route
	 *
	 * @param string $methods
	 * @param string $url
	 * @param object|callback $callback
	 * 
	 * @return void
	 */
	private static function add($methods, $url, $callback) {
		$url = trim($url, '/');
		$url = rtrim(static::$prefix . '/' . $url, '/');
		$url = $url?:'/';
		foreach (explode('|', $methods) as $method) {
			static::$routes[] = [
				'url' => $url,
				'callback' => $callback,
				'method' => $method,
				'middleware' => static::$middleware,
			];
		}
	}

	/**
	 * Add new get route
	 *
	 * @param string $url
	 * @param object|callback $callback
	 * 
	 * @return void
	 */
	public static function get($url, $callback) {
		static::add('GET', $url, $callback);
	}

	/**
	 * Add new post route
	 *
	 * @param string $url
	 * @param object|callback $callback
	 * 
	 * @return void
	 */
	public static function post($url, $callback) {
		static::add('POST', $url, $callback);
	}

	/**
	 * Add any get route
	 *
	 * @param string $url
	 * @param object|callback $callback
	 * 
	 * @return void
	 */
	public static function any($url, $callback) {
		static::add('GET|POST', $url, $callback);
	}

	/**
	 * Set prefix for routing
	 * 
	 * @param string $prefix
	 * @param callback $callback
	 *
	 * @return array
	 */
	public static function prefix($prefix, $callback) {
		$parent_prefix = static::$prefix;
		static::$prefix .= '/' . trim($prefix, '/');
		if (is_callable($callback)) {
			call_user_func($callback);
		} else {
			//throw new \Exception("Please provide valid callback function");
			throw new \BadFunctionCallException("Please provide valid callback function");
			
		}
		static::$prefix = $parent_prefix;
	}

	/**
	 * Set middleware for routing
	 * 
	 * @param string $middleware
	 * @param callback $callback
	 *
	 * @return array
	 */
	public static function middleware($middleware, $callback) {
		$parent_middleware = static::$middleware;
		static::$middleware .= '|' . trim($middleware, '|');
		if (is_callable($callback)) {
			call_user_func($callback);
		} else {
			//throw new \Exception("Please provide valid callback function");
			throw new \BadFunctionCallException("Please provide valid callback function");
			
		}
		static::$middleware = $parent_middleware;
	}

	/**
	 * Handle the request and match the routes
	 * 
	 * @return mixed
	 */
	public static function handle() {
		$url = Request::url();
		
		foreach (static::$routes as $route) {
			$matched = true;
			$route['url'] = preg_replace('/\/{(.*?)}/', '/(.*?)', $route['url']);
			$route['url'] = '#^' . $route['url'] . '$#';
			if (preg_match($route['url'], $url, $matches)) {
				array_shift($matches);
				$params = array_values($matches);
				foreach ($params as $param) {
					if (strpos($param, '/')) {
						$matched = false;
					}
				}
				//return $params;
				if ($route['method'] != Request::method()) {
					$matched = false;
				}

				if ($matched == true) {
					return static::invoke($route, $params);
				}
			}
		}
		//return $url;
		//die('Not found page');
		return View::render('errors.404');
	}

	/**
	 * Invoke the route
	 * 
	 * @param array $route
	 * @param array $params
	 */
	public static function invoke($route, $params = []) {
		static::executeMiddleware($route);
		$callback = $route['callback'];
		if (is_callable($callback)) {
			return call_user_func_array($callback, $params);
		} elseif (strpos($callback, '@') !== false) {
			list($controller, $method) = explode('@', $callback);
			$controller = 'App\Controllers\\' . $controller;
			if (class_exists($controller)) {
				$object = new $controller;
				if (method_exists($object, $method)) {
					return call_user_func_array([$object, $method], $params);
				} else {
					throw new \BadFunctionCallException("The method " . $method . " is not exists at " . $controller);
				}
			} else {
				throw new \ReflectionException("class " . $controller . " is not found");
			}
		} else {
			throw new \InvalidArgumentException("Please provide valid callback function");
			
		}
	}

	/**
	 * Execute middleware
	 * 
	 * @param array $route
	 * 
	 */
	public static function executeMiddleware($route) {
		foreach (explode('|', $route['middleware']) as $middleware) {
			if ($middleware != '') {
				$middleware = 'App\Middleware\\' . $middleware;
				if (class_exists($middleware)) {
					$object = new $middleware;
					call_user_func_array([$object, 'handle'], []);
				} else {
					throw new \ReflectionException("class " . $middleware . " is not found");
					
				}
			}
		}
	}





	/**
	 * Set prefix for routing
	 * 
	 * @return array $routes
	 */
	public static function allRoutes() {
		return static::$routes;
	}

}

?>