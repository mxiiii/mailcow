<?php
class BaseController
{
	public function run($template)
	{
		if(is_file(strtolower(TEMPLATE_PATH.$template['controller'].'/'.$template['action'].'.tpl')))
		{
			Core::$template->assign('content', strtolower($template['controller'].'/'.$template['action'].'.tpl'));
			Core::$template->display('layout.tpl');
		}
		else
		{
			throw new Exception('1400: Template not found');
		}
	}
}
?>