<?php
require('library/common.php');

// Überprüfen ob der Pfad der aufgerufen wurde auch als Route gibt
if(Router::checkRoute($_SERVER['REQUEST_URI']))
{

	// Route bkeommen und trennen um die einzelnen Sachen zu bkeommen
	$method = explode('@', Router::getRoute($_SERVER['REQUEST_URI']));

	// Überprüfen ob die in der Route angegebene Methode auch die durchgeführte Methode ist
	if($method[0] === strtolower($_SERVER['REQUEST_METHOD']))
	{

		// Methode, Controller und Aktionen in einem Array speichern
		$route = [];
		$route['method'] = $method[0];
		$route['controller'] = ucfirst(explode('#', $method[1])[0]);
		$route['action'] = explode('#', $method[1])[1];

		// Controller als 'Controller' abspeichern
		$controller = $route['controller'].'Controller';

		// Überprüfen ob der Controller geladen wurde. Falls nicht, Controller laden und starten
		if(is_file(CONTROLLERS_PATH.$controller.'.php') && !class_exists($controller))
		{
			include(CONTROLLERS_PATH.$controller.'.php');
			Core::$controller = new $controller;
		}
		else
		{
			throw new Exception('404');
		}

		// nochmal überprüfen ob der Controller exestiert
		if(class_exists($controller))
		{

			// Überprüfen ob die Aktion exestiert
			if(method_exists((new $controller), $route['action']))
			{

				// Falls alles ok ist, Template darstellen
				(new $controller)->$route['action']();
				Core::$controller->show($route);
			}
			else
			{
				throw new Exception('1338: Action \''.$route['action'].'\' not found');
			}
		}
		else
		{
			throw new Exception('1337: Controller \''.$controller.'\' not found');
		}
	}
	else
	{
		throw new Exception('1339: \''.$_SERVER['REQUEST_METHOD'].' -> '.$_SERVER['REQUEST_URI'].'\' Route not found');
	}
}
else
{
	throw new Exception('1339: \''.$_SERVER['REQUEST_URI'].'\' Route not found');
}

?>