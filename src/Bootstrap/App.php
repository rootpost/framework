<?php

namespace Mvc\Bootstrap;

use Mvc\Exceptions\Whoops;
use Mvc\Session\Session;
//use Mvc\Cookie\Cookie;
use Mvc\Http\Server;
use Mvc\Http\Request;
use Mvc\Http\Response;
use Mvc\File\File;
use Mvc\Router\Route;

/**
 * 
 */
class App {
	
	private function __construct() {}
	/**
	 * Run the application
	 */
	public static function run() {
		// Register whoops
		Whoops::handle();

		// Start session
		Session::start();

		// Handle request
		Request::handle();

		// Require routes
		File::require_directory('routes');

		// Handle the route
		$data = Route::handle();

		//
		Response::output($data);

		//echo "<pre>";
		//print_r(Route::allRoutes());
		//echo "</pre>";

		//echo Server::get('DOCUMENT_ROOT');

	}
}

?>