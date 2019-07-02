<?php
error_reporting(E_ALL);
if(! defined("avalanche_FULLAPP_PHP")){

 define("ROOT", "/local/home/invers/public_html/thewulfs/");
 define("PUBLICHTML", "/local/home/invers/public_html/");
 define("HOSTURL", "http://inversiondesigns.com/thewulfs/");
 define("DOMAIN", "inversiondesigns.com");
 define("APPPATH", "avalanche/");

 //set SECURE to 1 if cookies need to be sent over https connection
 define("SECURE", "0");
 define("INCLUDEPATH", "includes/");
 define("JAVASCRIPT", "javascript/");
 define("MODULES", "modules/");
 define("SKINS", "skins/");
 define("LIBRARY", "library/");
 define("CLASSLOADER", "classloader/");
 define("HOST", "localhost");
 define("ADMIN", "USERNAME HERE");
 define("PASS", "PASSWORD HERE");
 define("DATABASENAME", "DATABASE HERE");
 define("PREFIX", "avalanche_");
 //define("ACCOUNT", "awulf");

// include_once ROOT . APPPATH . INCLUDEPATH . "include.mailman.php";
 include_once ROOT . APPPATH . INCLUDEPATH . "include.php";

}
?>
