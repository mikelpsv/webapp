<?php

if(ini_get('register_globals') == 'on') ini_set('register_globals', 'off');

define('GL_CLASS_AUTOLOAD', true);

/**
* Возвращает путь до файла класса
* @param string $classname имя класса
* @return string полный путь к файлу
*/
function classPath($classname)
{
    $class_data = explode('_', $classname);
    if (count($class_data) == 1) {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Data' .DIRECTORY_SEPARATOR  . $classname . '.php';
    } else {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR  . 'Data' .DIRECTORY_SEPARATOR  . $class_data[0] . DIRECTORY_SEPARATOR  . $class_data[1] . '.php';
    }
}

?>