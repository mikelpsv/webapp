<?php

/**
* Класс шаблонизатора
* @name Шаблонизатор
* @package core
* @version 3.0 $Id: class.template.php 6 2009-08-21 15:06:12Z lapshov $ 
*/

class Html_Template{
    public $tpl_dir = ''; 			//Путь к папке с шаблонами

    public $vars               = array();  // Массив с переменными
    public $tmplFile           = '';       // Имя компилируемого файла
    public $tmplCache          = '';       // Имя откомпилированного файла
    public $tpl_file_name         = '';
    public $cache;
    public $error_reporting     = 0;
    public $ext_tpl;						// Шаблон по-умолчанию (как текст)
	
	private $tagPrefix;
	private $tagSuffix;
    private $tagVariable;
    private $tagInclude;
    private $tagCondition;
    private $tagConditionForeach;
		
	private $template_source;

    public $bootstrap;
	 
    /**
    * @desc Конструктор
    * @param $tpl_file_name string
    * @param $dir string
    */
    public function __construct($tpl_file_name = '', $dir = '', $app = null)
    {
        $this->cache                = Core::App->Config()->templateCache;
        $this->tagPrefix			= '\{\{';
        $this->tagSuffix    		= '\}\}';
        $this->tagVariable          = '\$.+';
        $this->tagInclude           = '[\'"]([^; ]+)[\'"]';
        $this->tagCondition         = '\( *(.*) *\)';
        $this->tagConditionForeach  = '\( *([^ ]+) .+\)';
		        
		// Имя файла шаблона можно задавать как в конструкторе, так и в процедуре парсинга (Parse)
        $this->tpl_file_name = $tpl_file_name;
        // Можно задать произвольную директорию, с шаблонами
        $this->tpl_dir = ( ($dir == '') ? DOC_ROOT . TPL_DIR : $dir );

		if(!file_exists($this->tpl_dir . '/' . '.cache')){
            mkdir($this->tpl_dir . '/' . '.cache', 0755);        
        }
        $this->error_reporting = error_reporting();

        $browser = Core::App()->getBrowser($_SERVER['HTTP_USER_AGENT']);

        if ($browser == 'IE 6.0' || $browser == 'IE 7.0' || $browser == 'IE 8.0' || $browser == 'IE 9.0') {
            $this->bootstrap = 2;
        } else {
            $this->bootstrap = 3;
        }
    }

    /**
    * @desc Включает/выключает кэширование шаблонов
    * @param b boolean
    */
    public function enableCache($b = true){
        $this->cache = $b;
    }
    
    private function addExp($tagPrefix, $pattern, $tagSuffix, $value){
        $key = "`{$tagPrefix}{$pattern}{$tagSuffix}`U";
        $this->expressions[$key] = $value;
        
    }
    
	private function processTemplate(){
		if (preg_match('`(<\?|\?>)`', $this->template_source)) 
            throw new template_exception(template::message(0));
            
  		$this->template_source = $this->findComments($this->template_source);
		$this->template_source = $this->findPlaceHolders($this->template_source);    
            
        $this->addExp($this->tagPrefix, "(if *{$this->tagCondition})" ,             $this->tagSuffix, '<?php $1 { ?>');
        $this->addExp($this->tagPrefix, "(elseif *{$this->tagCondition})",          $this->tagSuffix, '<?php } $1 { ?>');
        $this->addExp($this->tagPrefix, "(else)",                                   $this->tagSuffix, '<?php } $1 { ?>');
        $this->addExp($this->tagPrefix, "(foreach *{$this->tagConditionForeach})",  $this->tagSuffix, '<?php if(!empty($2)) $1 { ?>');
        $this->addExp($this->tagPrefix, "/(if|foreach)",                            $this->tagSuffix, '<?php } ?>');
        $this->addExp($this->tagPrefix, "({$this->tagVariable})",                   $this->tagSuffix, '<?php echo ($1); ?>');        
        $this->addExp($this->tagPrefix, "include {$this->tagInclude}",              $this->tagSuffix, '<?php echo $this->getTemplate("$1"); ?>');        
        $this->addExp($this->tagPrefix, "helper *\( *$.+\)(.*)",                    $this->tagSuffix, '<?php if (isset($1)) { $this->_setHelperData($1); } else { $this->_setHelperData(); } echo \$this$2; ?>');        
        $this->addExp($this->tagPrefix, "helper *{$this->tagCondition}(.*)",        $this->tagSuffix, '<?php $this->_setHelperData($1); echo \$this$2; ?>');                
		$this->template_source = preg_replace(array_keys($this->expressions), array_values($this->expressions), $this->template_source);
		return (string)$this->template_source;    
    }    
    
    /**
     * @desc Функция помещает переменную в общий массив
     * @param $name stirng - название переменной
     * @param $value mixed - ее значение
     */
    public function setVar($name,$value){
        $this->vars[$name] = $value;
    }
    
    public function setTemplateText($text = ''){
    	$this->ext_tpl = $text;
    }
    
    /**
    * @desc Синоним $this->setVar
    * @param $name string название переменной
    * @param $value mixed ее значение
    */
    public function assign($name, $value){
        $this->vars[$name] = $value;
    }

    /**
     * @desc Функция выводит все данные из общего массива на страницу. Используется для отладки
     * @return string;
     */
    public function _print(){
        print "<div align='left'><pre>";
        print_r($this->vars);
        print "</pre></div><br>";
    }

    /**
     * @desc Функция генерации страницы (один файл с шаблонами может иметь несколько шаблонов)
     * @param $filename string имя файла с нужным шаблоном
     * @param $tmplname string имя шаблона внутри файла
     */
    public function Parse($tpl_file_name = '', $tmplname = 'root')
    {
        if($tpl_file_name != ''){
            $this->tpl_file_name = $tpl_file_name;
        }
        
        //Проверка на существование файла
        if(!is_file($this->tpl_dir . '/' . $this->tpl_file_name) && trim($this->ext_tpl) == ''){
            echo '<div align="left"><br>';
            echo 'Ошибка: ' . 'Файл ' . $this->tpl_file_name . ' не является шаблоном или не найден.<br>';
            echo 'Скрипт: ' . __FILE__ . '<br>';
            echo 'Класс/Функция: ' . __CLASS__ . ' / ' . __FUNCTION__ . '<br>';
            echo 'Строка: ' . __LINE__ . '<br>';
            echo 'Доп. информация: ' . 'Каталог `' . $this->tpl_dir . '`, файл: `' . $this->tpl_file_name . '`</div>';
			return;
        }
        //Запоминаем имя файла с шаблонами
        $this->tmplFile = $this->tpl_dir . '/' . $this->tpl_file_name;
        //Запоминаем имя откомпилированного файла с шаблонами
        $this->tmplCache = $this->tpl_dir . '/.cache/' . $this->tpl_file_name;

        //Заносим название шаблона в зарезервированную переменную $TEMPLATE
        $TEMPLATE = $tmplname;

        //Создаем переменные из общего массива, чтобы они были видны в шаблоне
        foreach($this->vars as $k=>$v){
            $$k = $v;
        }
        $bootstrap = $this->bootstrap;
        
        // КЭШ работает только с шаблоном из файла
        if($this->tpl_file_name != ''){
			if( $this->cache){
	            //Смотрим время последнего обращения к файлу
	            $orig_time = filemtime($this->tmplFile);
                //echo $this->tmplFile;
                //echo ' ('.$orig_time.')';
	            //Если существует уже скомпилированная версия файла,
	            //то используем ее.
	            if(is_file($this->tmplCache)){
	                //Смотрим время последнего обращения к откомпилированному файлу
	                $cash_time = filemtime($this->tmplCache);
                    //echo '<br>' . $this->tmplCache;
                    //echo ' ('.$cash_time.')';

	                //Если оригинальный файл не изменился уже после того, как он был откомпилирован,
	                //то используем откомпилированную версию.

	                if($cash_time < $orig_time){
                        //Включаем кэширование
	                    $this->error_reporting = error_reporting();
	                    ob_start();
	                    //Отключаем вывод нотисов и варнингов
	                    error_reporting(E_ALL);
	                    //Подключаем шаблон
	                    include($this->tmplCache);

                        //Включаем вывод всех ошибок
	                    error_reporting($this->error_reporting);
	                    //Получаем сгенерированный текст
	                    $text = ob_get_contents(); ob_end_clean();
	                    return $text;
	                }
	            }
	        }
	        //Если откомпилированный файл не существует или оригинальный файл
	        //успел измениться после компиляции, то компилируем файл заново
	
	        //Получаем текст шаблонов
	        $text = file_get_contents($this->tmplFile);
        } else {
        	$text = &$this->ext_tpl;
        }

		//$this->processTemplate();
        $text = $this->findComments($text);
		$text = $this->findPlaceHolders($text);
		$text = preg_replace('/' . $this->tagPrefix . '(.*)\}\}/U', '<?php $1 ?>', $text);
		/*$text = str_replace(array('{{','}}'),array('<?','?>'), $text);*/
			
		//Записываем код в файл
        $f = fopen($this->tmplCache, 'w');
        fwrite($f, $text);
        fclose($f);

        // Выполняем код и возвращаем сгенерированную страницу
        ob_start();
        error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
 		eval('?>'.$text);
        error_reporting($this->error_reporting);
        $text = ob_get_contents(); ob_end_clean();
        return $text;
    }
    
    // РЕКУРСИЯ
	// Ищем PH если находим, проверяем в найденом
	public function findPlaceHolders($text){
		$text = preg_replace_callback('/' . $this->tagPrefix . '\s*placeHolder\s*:\s*(\S*)\s*' . $this->tagSuffix . '(.*)' . $this->tagPrefix . '\/placeHolder\s*' . $this->tagSuffix . '/si', array($this, 'parsePlaceHolders'), $text);
		if(preg_match('/' . $this->tagPrefix . '\s*placeHolder\s*:\s*(\S*)\s*' . $this->tagSuffix . '(.*)' . $this->tagPrefix . '\/placeHolder\s*' . $this->tagSuffix . '/si', $text) ){
			$text = $this->findPlaceHolders($text);	
		}
		return $text;
	}
	
	public function parsePlaceHolders($match){
		if(count($match) < 2) return "";
		$n = "";
    	$txt = "$n<? if(isset($$match[1])): ?>$n";
    	$txt .= "<? while(\$row_of_$match[1] = $$match[1]->nextRow()): ?>$n";
    	$txt .= "<? extract(\$row_of_$match[1]); ?>$n";
    	$txt .= "{$match[2]}$n";
    	$txt .= "<? endwhile; ?>$n";
    	$txt .= "<? endif; ?>$n";
    	return $txt;
	}
	public function findComments($text){
		/*$text = preg_replace('/\{\{\s*comment\s*\}\}(.*)\{\{\/comment\s*\}\}/si', '<!--\\1-->', $text);*/
		$text = preg_replace('/' . $this->tagPrefix . '\s*comment\s*' . $this->tagSuffix . '(.*)' . $this->tagPrefix . '\/comment\s*' . $this->tagSuffix . '/si', '', $text);
		return $text;
	}

	public function parseTemplate(){
		
	}
	
	public function __toString(){
		return $this->Parse();
	}
	
    /**
    * @desc Возвращает текст скомпилированного шаблона
    * @param $filename string имя файла с нужным шаблоном
    * @param $tmplname string имя шаблона внутри файла
    * @return string
    */
    public function text($tpl_file_name = '', $tmplname = 'root'){
        return $this->Parse($tpl_file_name, $tmplname);
    }
    
    /**
    * @desc Выводит в stdout скомпилированный шаблон
    * @param $filename string имя файла с нужным шаблоном
    * @param $tmplname string имя шаблона внутри файла
    */
    public function out(){
        echo $this->Parse($tpl_file_name, $tmplname);
    }

    /**
    * @desc Выводит в файл $fname скомпилированный шаблон
    * @param $filename string имя файла с нужным шаблоном
    * @param $tmplname string имя шаблона внутри файла
    * @param $fname string полный путь к файлу
    */
    public function out_file ($tpl_file_name, $tmplname, $fname) {
        if (!empty($bname) && !empty($fname) && is_writeable($fname)) {
            $fp = fopen($fname, 'w');
            fwrite($fp, $this->text($tpl_file_name, $tmplname));
            fclose($fp);
        }
    }
}
?>
