<?php

/**
 * Core Class
 * desc
*/
class Core extends medoo
{
	public static $link = false;
	public static $vars = [];


	/**
	 *
	 *
	*/
	public function __construct($mysql_host, $mysql_user, $mysql_password, $mysql_database)
	{
		/* Database Setup */
		self::$link = new medoo([
			'database_type' => 'mysql',
			'database_name' => $mysql_database,
			'server' => $mysql_host,
			'username' => $mysql_user,
			'password' => $mysql_password,
			'charset' => 'utf8'
		]);
	}


	/**
	 * Function to set the Data
	*/
	public static function setVar($var, $value)
	{
		if(!empty($var) && !empty($value))
		{
			self::$vars[$var] = $value;
		}
		else
		{
			return false;
		}
	}


	/**
	 * Function to get the Data
	*/
	public static function getVar($var)
	{
		if(!empty($var))
		{
			if(isset(self::$vars[$var]))
			{
				return self::$vars[$var];
			}
		}
		else
		{
			return false;
		}
	}

}
?>