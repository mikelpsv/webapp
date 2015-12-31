<?php

class Config{

    private $__data;

    public function __construct(){
	$this->__data = array();
    }

    public function __set($name, $value)
    {
        $this->__data[$name] = $value;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->__data)) {
            return $this->__data[$name];
        } else {
            return null;
        }
    }

    public function loadConfig()
    {
        $file = DOC_ROOT . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . ENVIRONMENT . '.php';
        if (file_exists($file)) {
            $this->__data = include_once($file);
        }
    }

}

?>
