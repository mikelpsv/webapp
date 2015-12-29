<?php
/*
Базовый класс контроллера
*/

/** @property App $app */
/** @property Model_Cart $cart */

class Controller
{
    protected $action;
    protected $subaction;
    protected $params;
    protected $app;
    protected $layout;
    protected $response;

    protected $path;
    protected $context;

    public $sid;
    public $sort;

    public $cart;
    
    public function __construct()
    {
        $this->sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : null;
    }

    public function Init()
    {
    }

    public function setApp(&$app)
    {
        $this->app = $app;

        if (isset($_REQUEST['debugger']) && $_REQUEST['debugger']) {
            $_SESSION['debugger'] = 1;
        } elseif (isset($_REQUEST['debugger'])) {
            unset($_SESSION['debugger']);
        }
        $this->app->isDebugger = isset($_SESSION['debugger']) ? true : false;
    }

    public function setAction($action)
    {
        $this->action = ucfirst($action);
    }

    public function setSubAction($subaction)
    {
        $this->subaction = ucfirst($subaction);
    }

    public function setParams($params)
    {
        $this->params = $params;
    }    
    
    public function Process()
    {
        if($this->action == ''){
            $this->action = 'Default';
        }
        if(is_null($this->subaction)){
            $method_name = 'action' . ucfirst($this->action);
        }else{
            $method_name = 'action' . ucfirst($this->action) . ucfirst($this->subaction);
        }
        //echo $method_name;
        if(method_exists($this, $method_name)){
            return call_user_func(array($this, $method_name));
        }else{
            $this->app->WriteLog('Метод не найден: ' . $method_name, 0, $_SESSION['Office']['ClientGUID'] . '.' . $_SESSION['Office']['filial']);
        }
    }
    
    public function createView($action = '')
    {
        if ($action != '') {
            $view_name = get_class($this) . '_' . $action . '.tpl';
        } else {
            if (!is_null($this->subaction)) {
                $view_name = get_class($this) . '_' . $this->action . $this->subaction . '.tpl';
            } else {
                $view_name = get_class($this) . '_' . $this->action . '.tpl';
            }
        }
        // /Res/Класс_Action.tpl
        
        //echo $view_name; exit;

        return new Html_Template($view_name, $this->app->getDocRoot() . TPL_DIR, $this->app);
    }

    public function checkLogin()
    {
        if (!isset($_SESSION['Office']['SID'])) {
            $_SESSION['Office']['SID'] = '';
        }

        if ($this->sid == '') {
            if ($_SESSION['Office']['SID'] == '') {
                if ($this->app->isAjaxRequest()) {
                    $response = ['status' => -1];
                    echo json_encode($response);
                    exit;
                } else {
                    $this->app->Location('/login/');
                }
            } else {
                $this->sid = $_SESSION['Office']['SID'];
            }
        }
    }

    public function sortData(&$data)
    {
        $sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : null;
        if ($sort) {
            $sorting = explode('_', $sort);
            $dir = Helper_ArrayHelper::element(1, $sorting);
            if (is_null($dir)) {
                $dir = SORT_ASC;
            }
            $cols = explode('-', $sorting[0]);
            Helper_ArrayHelper::sortByColumn($data, $cols[0], Helper_ArrayHelper::element(1, $cols), $dir);
        }
    }
}

?>