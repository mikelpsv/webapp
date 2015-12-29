<?php

class MetingRooms_Calendar{

  public getDay($date){
    $day_array = array();
    
    for(i=9;i<=19;i++){
      $day_array[i]     = array();
      $day_array[i+0.5] = array();
    }  
    return $day_array;
  } 
  public getWeek(){
    
  }
  public getMonth(){
    
  }
}

?>