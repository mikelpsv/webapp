<?php
register_shutdown_function('shutdown');


function shutdown()
{
    $error = error_get_last();

    if ($error['type'] === E_CORE_ERROR) {
        //header('Location: /');
        echo 'Произошла ошибка обработки запроса. Попробуйте обновить страницу.';
        //do your shutdown stuff here
        //be care full do not call any other function from within shutdown function
        //as php may not wait until that function finishes
        //its a strange behavior. During testing I realized that if function is called
        //from here that function may or may not finish and code below that function
        //call may or may not get executed. every time I had a different result.

        // e.g.

    }

}

/*
 *---------------------------------------------------------------
 * Окружение приложения
 *---------------------------------------------------------------
 *
 * Для загрузки различных конфигураций в зависимости от типа окружения
 *
 *     development
 *     testing
 *     production
 *
 */
define('ENVIRONMENT', 'production');

/*
*---------------------------------------------------------------
* ERROR REPORTING
*---------------------------------------------------------------
*
* Управление показами ошибок.
*/

if (defined('ENVIRONMENT'))
{
    switch (ENVIRONMENT)
    {
        case 'development':
            error_reporting(E_ALL);
            define('DEBUG', true);
            break;

        case 'testing':
            error_reporting(E_NOTICE);
            define('DEBUG', true);
            break;
        case 'production':
            error_reporting(0);
            define('DEBUG', false);
            break;

        default:
            exit('Не установлен тип окружения.');
    }
}

if($_SERVER['REMOTE_ADDR'] == '192.168.5.94'){
   // var_dump($_SERVER);
}
define('DOC_ROOT', dirname(__FILE__));


require_once 'common.php';

$Db     = &Core::Db(ENVIRONMENT);
$App    = &Core::App($Db);

echo $App->Process();
    

?>
