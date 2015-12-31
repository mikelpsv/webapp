<?php

/** @property Db_MySql $Db*/

class App
{

    private $debug_text;
    private $Db;
    private $User;
    private $__request_uri;
    // Корень приложения (где располагается index.php)
    public $__doc_root;
    public $__headers;

    public $__map;
    public $__data;
    public $__actions;
    public $__response;
    public $__langs;

    public $__rlang;
    public $__rcontroller;
    public $__raction;
    public $__rsubaction;

    private $style;

    public $layout;

    public $config;

    private $log;

    public $isDebugger;

    const APPKEY = 'aslkjuwe23k';

    public function __construct(&$Db = null)
    {
        $this->Db = $Db;

        session_start();

        $this->__response 	= '';
        $this->__data 		= array();
        $this->__actions 	= array();
        $this->__map 		= array();
        $this->__langs 		= array('ru' => '', 'en' => '');

        // Загрузка конфигурационного файла
        
        if (!GL_CLASS_AUTOLOAD) {
	    require_once classPath('Config');
        }
	$this->config = new Config();
	$this->config->loadConfig();


        $this->parseRequest();

        setlocale(LC_ALL, $this->config->locale);
        $this->Headers('Content-type', 'text/html; charset=utf-8');

        if (!GL_CLASS_AUTOLOAD) {
	    require_once classPath('User');
        }
        $this->User = new User($_SESSION);
    }

    public function parseRequest()
    {
        $doc_root = dirname($_SERVER['SCRIPT_FILENAME']);
        if (DIRECTORY_SEPARATOR == '\\') {
            $doc_root = str_replace('/', DIRECTORY_SEPARATOR, $doc_root);
        }
        $this->__doc_root = $doc_root;

	// Убираем каталоги установки, если разместили не в корне
        $__request_uri 	= explode('/', trim($_SERVER['REQUEST_URI'], '/'));
	$__base_setup 	= explode('/', trim($this->config->base_setup, '/'));
	
	foreach($__base_setup as $k=>$v){
		if($__request_uri[$k] == $v){
			array_shift($__request_uri);
		}
	}
	// Проверяем указание языка, если есть
	if (isset($this->__langs[$__request_uri[0]])) {
		$this->__rlang = $__request_uri[0];
		array_shift($__request_uri);
	}

        $this->__rcontroller = (isset($__request_uri[0]) && $__request_uri[0] != '') ? $__request_uri[0] : 'main';
        $this->__raction = isset($__request_uri[1]) ? $__request_uri[1] : 'Default';
        $this->__rsubaction = (isset($__request_uri[2]) && $__request_uri[2] != '?') ? $__request_uri[2] : '';


        if (true) {
            $this->Debug('Document root: ' . $this->__doc_root);
            $this->Debug('Request uri: ' . $_SERVER['REQUEST_URI']);
            $this->Debug('Request uri: ' . var_dump($__request_uri));


            $this->Debug('Language: ' . $this->__rlang);
            $this->Debug('Controller: ' . $this->__rcontroller);
            $this->Debug('Action: ' . $this->__raction);
            $this->Debug('SubAction: ' . $this->__rsubaction);
        }


        $this->__data['layout'] = array();
    }

    public function Location($location)
    {
        header('Location: ' . $location);
    }

    public function User()
    {
        return $this->User;
    }

    public function Config(){
	return $this->config;
    }

    public function getFileController()
    {
        $controller = $this->__doc_root . DIRECTORY_SEPARATOR . 'Controller' . DIRECTORY_SEPARATOR . ucfirst($this->__rcontroller) . '.php';

        if (file_exists($controller)) {
            return $controller;
        } else {
            return '';
        }
    }

    public function setPath($path, $controller)
    {
        $this->__map[$path] = $controller;
    }

    public function Headers($key, $value)
    {
        $this->__headers[$key] = $value;
    }

    public function Process()
    {

        // Здесь мы должны:
        // 1. Создать объекты всех контроллеров, 
        // используемых на странице и запустить их на выполнение

        // 2. Подготовить данные

        // 3. Отправить заголовки 

        // 4. Отправить данные

        // Получаем путь к файлу и загружаем
        $file = $this->getFileController();

        if($file == '') {echo $file . ' is not file!'; exit;}
	require_once $file;

        $contr_name = ucfirst($this->__rcontroller);

        // Создаем объект контроллера, передаем параметры и запускаем на выполнение
        /** @var Controller $controller */
        $controller = new $contr_name;

        // Передаем ссылку в контроллер
        $controller->setApp($this);
        // Передаем действие для выполнения
        $controller->setAction($this->__raction);
        // Передаем поддействие
        $controller->setSubAction($this->__rsubaction);
        // var_dump($this->__rsubaction);

        // Передаем параметры выполнения (пока так для наглядности)
        //$controller->setParams($this->__request_uri);

        // Т.н. конструктор
        $controller->Init();

        $controller->Process();

        // Отправляем заголовки
        foreach ($this->__headers as $k => $v) {
            header($k . ': ' . $v);
        }
        // Если тело ответа не заполнено, то выводим шаблон
        if ($this->__response == '') {
            $o_layout = new Html_Template('main.tpl', $this->__doc_root . TPL_DIR, $this);
            foreach ($this->layout as $k => $v) {
                $o_layout->assign($k, $v);
            }
            return (string)$o_layout;
        } else {
            return $this->__response;
        }

    }

    /*
    public function processAction($controller){
        return $controller->Process();
    }
    */
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

    public function getBrowser($agent = '')
    {
        if (empty($agent)) $agent = $_SERVER['HTTP_USER_AGENT'];

        // регулярное выражение, которое позволяет отпределить 90% браузеров
        preg_match("/(MSIE|Opera|Firefox|Chrome|Version|Opera Mini|Netscape|Konqueror|SeaMonkey|Camino|Minefield|Iceweasel|K-Meleon|Maxthon)(?:\/| )([0-9.]+)/", $agent, $browser_info);

        if (is_array($browser_info) && count($browser_info)) {
            list($full, $browser, $version) = $browser_info; // получаем данные из массива в переменную

            // определение _очень_старых_ версий Оперы (до 8.50), при желании можно убрать
            if (preg_match("/Opera ([0-9.]+)/i", $agent, $opera)) return 'Opera ' . $opera[1];
            if ($browser == 'MSIE') { // если браузер определён как IE
                preg_match("/(Maxthon|Avant Browser|MyIE2)/i", $agent, $ie); // проверяем, не разработка ли это на основе IE
                if ($ie) return $ie[1] . ' based on IE ' . $version; // если да, то возвращаем сообщение об этом
                return 'IE ' . $version; // иначе просто возвращаем IE и номер версии
            }
            if ($browser == 'Firefox') { // если браузер определён как Firefox
                preg_match("/(Flock|Navigator|Epiphany)\/([0-9.]+)/", $agent, $ff); // проверяем, не разработка ли это на основе Firefox
                if ($ff) return $ff[1] . ' ' . $ff[2]; // если да, то выводим номер и версию
            }
            if ($browser == 'Opera' && $version == '9.80') return 'Opera ' . substr($agent, -5); // если браузер определён как Opera 9.80, берём версию Оперы из конца строки
            if ($browser == 'Version') return 'Safari ' . $version; // определяем Сафари
            if (!$browser && strpos($agent, 'Gecko')) return 'Browser based on Gecko'; // для неопознанных браузеров проверяем, если они на движке Gecko, и возращаем сообщение об этом
            return $browser . ' ' . $version; // для всех остальных возвращаем браузер и версию
        } else {
            return null;
        }
    }

    public function __toString()
    {
    }

    public function getDocRoot()
    {
        return $this->__doc_root;
    }


    public function WriteLog($msg_text, $msg_type = 0, $user_quid = '', $logging = true)
    {
        if (!is_object($this->log)) {
            if (!GL_CLASS_AUTOLOAD) {
                require_once classPath('Log');
            }
            $this->log = new Log($this->Db);
        }
        $this->log->Write($msg_type, $msg_text, $user_quid, $this->getBrowser($_SERVER['HTTP_USER_AGENT']), $logging);
    }

    public function Debug($message)
    {
	echo $message . '<br>';
    }

    /*
    *---------------------------------------------------------------
    * Загрузка конфигурации для окружения
    *---------------------------------------------------------------
    *
    */
    private function loadConfig()
    {
      /*
	$file = DOC_ROOT . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . ENVIRONMENT . '.php';
        if (file_exists($file)) {
            $this->config = include_once($file);
        } */
    }

    /**
     * Проверка на запрас AJAX
     *
     * @return 	bool
     */
    public function isAjaxRequest()
    {
        return ( ! empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }
}

//require_once classPath('User');
?>
