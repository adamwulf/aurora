<?
  class ClassLoader{
	protected $classpath;

	public function __construct(){
		$this->classpath = array(ROOT . APPPATH . LIBRARY);
	}

	public function load($classname){
		for($i=0;$i<count($this->classpath);$i++){
			$path = $this->classpath[$i];
			$this->load_recursive($path, $classname);
		}
	}

	private function load_recursive($classpath, $classname){
		$theList = array();
		if ($handle = opendir($classpath)) {
    		while (false != ($file = readdir($handle))) {
		       	if ($file != "." && $file != "..") {
				if(is_dir($classpath . $file)){
					$this->load_recursive($classpath . $file . "/", $classname);
				}else{
					if($file == "class.$classname.php"){
						include_once $classpath . $file;
					}else
					if($file == "interface.$classname.php"){
						include_once $classpath . $file;
					}
				}
        		}
		}
		closedir($handle);
		unset($handle); 
		}
	}
  }

  function __autoload($classname){
	try{
		$ClassLoader = new ClassLoader();
		$ClassLoader->load($classname);
	}catch(Exception $e){
		print_r($e);
	}
  }
?>