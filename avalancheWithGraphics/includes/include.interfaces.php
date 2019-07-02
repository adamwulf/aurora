<?
	$realDir = ROOT . APPPATH . INCLUDEPATH . "interfaces/";
	$theList = array();
	if ($handle = opendir($realDir)) {
	    while (false != ($file = readdir($handle))) {
        	if ($file != "." && $file != "..") {
			if(is_dir($realDir . $file)){
				if(file_exists($realDir . $file . "/interface." . $file . ".php")){
					$inc_file = $realDir . $file . "/interface." . $file . ".php";
					require $inc_file;
				}
			}else{
				//is file, so noop
			}
	        }
	    }
	    closedir($handle);
	    unset($handle); 
	 }
?>