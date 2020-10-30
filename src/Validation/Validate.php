<?php

namespace Mvc\Validation;

use Rakit\Validation\Validator;
use Mvc\Http\Request;
use Mvc\Url\Url;

class Validate {
	/**
	 * Validation constructor
	 */
	private function __construct() {}

	/**
	 * Validate request
	 * 
	 * @param array $rules
	 * @param bool $json
	 * 
	 * @return mixed
	 */
	public static function validate(Array $rules, $json) {
		$validator = new Validator;

		$validation = $validator->validate($_POST + $_FILES, $rules);

		$errors = $validation->errors();

		if ($validation->fails()) {
			if ($json) {
				return ['errors' => $errors->firstOfAll()];
			} else {
				Session::set('errors', $errors);
				Session::set('old', Request::all());
				return Url::redirect(Url::previous());
			}
			// handling errors
			/*
			$errors = $validation->errors();
			echo "<pre>";
			print_r($errors->firstOfAll());
			echo "</pre>";
			exit;
			*/
		}
	}

}

?>