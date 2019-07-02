<?
	$realDir = ROOT . APPPATH . INCLUDEPATH . "datastructures/";
	$theList = array();
	if ($handle = opendir($realDir)) {
	    while (false != ($file = readdir($handle))) {
        	if ($file != "." && $file != "..") {
			if(is_dir($realDir . $file)){
				if(file_exists($realDir . $file . "/structure." . $file . ".php")){
					$inc_file = $realDir . $file . "/structure." . $file . ".php";
					include $inc_file;
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