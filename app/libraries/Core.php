<?php
/*
* App Core Class
* Creates URL & loads core controller
* URL FORMAT - /controller/method/params
*/
class Core {
	protected $currentController = 'Pages';
	protected $currentMethod = 'index';
	protected $params = [];

	public function __construct()
	{
		// print_r($this->getUrl());
		$url = $this->getUrl();

		// Look in controllers for first value
		if (file_exists('../app/controllers/' . ucwords($url[0]) . '.php')) {
			// if exists, set as controller
			$this->currentController = ucwords($url[0]);
			// Unset 0 Index
			unset($url[0]);
		}
		// Require the controller
		require_once '../app/controllers/' . $this->currentController . '.php';
		// Instantiate controller class
		$this->currentController = new $this->currentController;

		// Check second part of url
		if (isset($url[1])) {
			// Check for method in currentController
			if (method_exists($this->currentController, $url[1])) {
				$this->currentMethod = $url[1];
				// Unset 1 index
				unset($url[1]);
			}
		}
		// echo $this->currentMethod;

		// Get params
		$this->params = $url ? array_values($url) : [];

		// Call a callback with array of params
		call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
	}

	/*
	* $_GET, trim, & sanitize url
	* @return Array $url
	*/
	public function getUrl()
	{
		if (isset($_GET['url'])) {
			$getUrl = htmlentities($_GET['url']);
			$url = rtrim($getUrl, '/');
			$url = filter_var($url, FILTER_SANITIZE_URL);
			$url = explode('/', $url);
			return $url;
		}
	}
}