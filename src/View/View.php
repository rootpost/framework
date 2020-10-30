<?php

namespace Mvc\View;

use Mvc\File\File;
use Mvc\Session\Session;
use Jenssegers\Blade\Blade;

class View {
	/**
	 * View constructor
	 */
	private function __construct() {}

	/**
	 * Render view
	 *
	 * @param string $path
	 * @param array $data
	 * @return string
	 */
	public static function render($path, $data = []) {
		$errors = Session::flash('errors');
		$old = Session::flash('old');
		$data = array_merge($data, ['errors' => $errors, 'old' => $old]);
		return static::viewRender($path, $data);
		//return static::replaceRender($path, $data);
	}

	/**
	 * Render the view files using blade engine
	 *
	 * @param string $path
	 * @param array $data
	 * @return string
	 */
	public static function bladeRender($path, $data = []) {
		$blade = new Blade(File::path('views'), 'storage/cache');
		return $blade->make($path, $data)->render();
	}

	/**
	 * Render view file
	 *
	 * @param string $path
	 * @param array $data
	 * @return string
	 */
	public static function viewRender($path, $data = []) {
		$path = 'views' . File::ds() . str_replace(['/', '\\', '.'], File::ds(), $path) . '.php';
		if ( !File::exists($path)) {
			throw new \Exception("The view file {$path} is not exist");
		}

		ob_start();
		extract($data);
		//File::include_file($path);
		include File::path($path);
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	}

	/**
	 * Render replace file
	 *
	 * @param string $path
	 * @param array $data
	 * @return string
	 */
	public static function replaceRender($path, $data = []) {

        $path = 'views' . File::ds() . str_replace(['/', '\\', '.'], File::ds(), $path) . '.php';
		if ( !File::exists($path)) {
			throw new \Exception("The view file {$path} is not exist");
		} else {
			$loadtpl = file_get_contents(File::path($path));
		}

        foreach ($data as $key => $value) {
            $loadtpl = str_replace("{{ ".$key." }}", $data[$key], $loadtpl);
        }
        return $loadtpl;
	}
}

?>