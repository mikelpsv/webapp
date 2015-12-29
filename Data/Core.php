<?php

class Core{

	public static function &App($def_site = '')
	{
		static $instance;
		if(!is_object($instance)){
			if(!GL_CLASS_AUTOLOAD){
				require_once classPath('App');
			}
			$instance = new App($def_site);
		}
		return $instance;
	}
	
	public static function &Admin($def_site = '')
	{
		static $instance;
		if(!is_object($instance)){
			if(!GL_CLASS_AUTOLOAD){
				require_once classPath('App_Admin');
			}
			$instance = new App_Admin($def_site);
		}
		return $instance;
	}
	
	public static function &Template($tpl_file_name = '', $dir = ''){
		static $instance;
		if(!is_object($instance)){
			if(!GL_CLASS_AUTOLOAD){
				require_once classPath('Html_Template');
				
			}
			$instance = new Html_Template($tpl_file_name, $dir, self::App());
		}
		return $instance;		
	}


	public static function &Db($ENV){

		static $instance;
		if(!is_object($instance)){
			if(!GL_CLASS_AUTOLOAD){
				require_once classPath('Db_MySql');
				
			}
			$file_config = DOC_ROOT . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . $ENV . '_db.php';
			if (file_exists($file_config)) {
			    $cfg = include_once($file_config);	
			}

			$instance = new Db_MySql($cfg['DB_HOST'], $cfg['DB_USER'], $cfg['DB_PASS'], $cfg['DB_NAME'], false);
		}
		return $instance;		
	}

}

?>
