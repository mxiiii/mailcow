<?php
require('library/common.php');
// $content = false;

// switch($content)
// {
// 	default:
// 		$content = 'index.tpl';
// 		break;
// }

// $template->assign('content', $content);
// $template->display('layout.tpl');


if(Router::checkRoute($_SERVER['REQUEST_URI']))
{
	$method = explode('@', Router::getRoute($_SERVER['REQUEST_URI']));
	if($method[0] === strtolower($_SERVER['REQUEST_METHOD']))
	{
		$route = [];
		$route['method'] = $method[0];
		$route['controller'] = ucfirst(explode('#', $method[1])[0]);
		$route['action'] = explode('#', $method[1])[1];

		$controller = $route['controller'].'Controller';

		if(is_file(CONTROLLERS_PATH.$controller.'.php') && !class_exists($controller))
		{
			include(CONTROLLERS_PATH.$controller.'.php');
			Core::$controller = new $controller;
		}

		if(class_exists($controller))
		{
			if(method_exists((new $controller), $route['action']))
			{
				Core::$controller->run($route);
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
		throw new Exception('1339: \''.$_SERVER['REQUEST_URI'].'\' Route not found');
	}
}
else
{
	throw new Exception('1339: \''.$_SERVER['REQUEST_URI'].'\' Route not found');
}

?>