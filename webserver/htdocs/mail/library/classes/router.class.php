<?php
class Router {

	private static $routes = [];

	public static function addRoute($route = '/', $method = 'get', $controller = 'welcome', $action = 'index')
	{
		if(!isset(self::$routes[$route]))
		{
			self::$routes[$route] = $method.'@'.$controller.'#'.$action;
		}
	}


	public static function checkRoute($route)
	{
		if(isset(self::$routes[$route]))
		{
			return true;
		}
		else
		{
			return false;
		}
	}


	public static function getRoute($route)
	{
		if(isset(self::$routes[$route]))
		{
			return self::$routes[$route];
		}
		else
		{
			throw new Exception('1337: Route not found');
		}
	}
}
?>