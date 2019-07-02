<?php
function getLongDate(){
        return date("D M j h:i:s a T Y");
}

function getShortDate(){
        return date("Y-m-d"); //1982-11-30
}

function getTime(){
        return date('H:m:s'); // 15:59:02
}

function getmicrotime(){ 
    list($usec, $sec) = explode(" ",microtime()); 
    return ((float)$usec + (float)$sec); 
    } 

?>