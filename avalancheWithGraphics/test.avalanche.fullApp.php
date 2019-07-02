<?
function find_test_files($suite, $path, $only=false){
	// if the $path comes in with a trailing /, then chop it off
	if(strrpos($path, "/") == (strlen($path)-1)){
//		$path = substr($path, 0, strlen($path)-1);
	}else{
		$path .= "/";
	}
	if ($handle = opendir($path)) {
	    while (false != ($file = readdir($handle))) {
        	if ($file != "." && $file != "..") {
//			echo "searching: " . $path . $file . "<br>";
			if(is_dir($path . $file)){
				find_test_files($suite, $path . $file, $only);
			}else{
				if(strpos($path . $file, "test.") !== false
				   && strpos($path . $file, "fullApp") === false
				   && (!$only || $only == $file)){
//					echo "including: " . $path . $file . "<br>";
					include_once $path . $file;
				}else
				if(strpos($file, "class.") === 0){
//					echo "including: " . $path . $file . "<br>";
					include_once $path . $file;
				}
			}
	        }
	    }
	    closedir($handle);
	    unset($handle); 
	 }
}
?>