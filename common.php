<?php

error_reporting(E_ALL);

if(ini_get('register_globals') == 'on') ini_set('register_globals', 'off');
spl_autoload_register('___autoload', true, true);

define('GL_CLASS_AUTOLOAD', true);

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR  . 'Data' . DIRECTORY_SEPARATOR . 'Core.php';

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

/**
 * Автоматическое подключение файлов классов. Функция используется при значении константы GL_CLASS_AUTOLOAD = true
 * @param string $classname имя класса
 */



function ___autoload($classname)
{
    $class_path = classPath($classname);
    if (file_exists($class_path)) {
        require_once $class_path;
    }
}

?>
