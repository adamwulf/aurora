<?

interface avalanche_interface_cookieTray {

   ///////////////////////////////////////
   // set's a cookie			//
   ///////////////////////////////////////
   abstract public function setCookie($name , $value = false, $expire = false, $path = false, $domain = false, $secure = false);

   ///////////////////////////////////////
   // get's a cookie			//
   ///////////////////////////////////////
   abstract public function getCookie($skin);

}


?>