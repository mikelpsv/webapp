<?php

class Main extends Controller
{
    public function __construct(){
        parent::__construct();
    }
    public function Init(){
	echo 'Main:Init<br>';
    }
	
    public function actionDefault(){
	echo 'Main:Default<br>';
    }
}

?>

