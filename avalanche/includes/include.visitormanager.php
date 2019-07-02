<?
//be sure to leave no white space before and after the php portion of this file
//headers must remain open after this file is included


//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////
//									//
//  include.visitormanager.php						//
//----------------------------------------------------------------------//
//  this class manages all the visitors for avalanche			//
//									//
//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////


include ROOT . APPPATH . INCLUDEPATH . "visitors/template/visitor.template.php";


	$realDir = ROOT . APPPATH . INCLUDEPATH . "visitors/";
	$theList = array();
	if ($handle = opendir($realDir)) {
	    while (false != ($file = readdir($handle))) {
        	if ($file != "." && $file != ".." && $file != "template") {
			if(is_dir($realDir . $file)){
				if(file_exists($realDir . $file . "/visitor." . $file . ".php")){
					$inc_file = $realDir . $file . "/visitor." . $file . ".php";
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

//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
///////////////                         //////////////////////////
///////////////    AVALANCHE VISITOR    //////////////////////////
///////////////         MANAGER         //////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
class avalanche_visitormanager {

	// a multidimensional array[0][name] and array[0][obj]
	private $_visitor_list;

	function sleep(){
	}

	//////////////////////////////////////////////////////////////////
	//  init($cal, $id)						//
	//--------------------------------------------------------------//
	//  input: $cal - the calendar object to which this visitor	//
	//		  belongs					//
	//  output: the id of this visitor				//
	//								//  
	//  precondition:						//  
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function __construct(){
	global $avalanche;
	$realDir = ROOT . APPPATH . INCLUDEPATH . "visitors/";
	$theList = array();
	if ($handle = opendir($realDir)) {
	    while (false != ($file = readdir($handle))) { 
        	if ($file != "." && $file != ".." && $file != "template") { 
			if(is_dir($realDir . $file)){
				if(file_exists($realDir . $file . "/visitor." . $file . ".php")){
					$obj_class = "visitor_" . $file;
					$obj = new $obj_class;
					$theList[] = array("name" => $file, "visitor" => $obj);
				}
			}else{
				//is file, so noop
			}
	        }
	    }
	    closedir($handle);
	    unset($handle); 
	 }

            $this->_visitor_list = $theList;
		
	}

	function getVisitors(){
		$ret = array();
		for($i=0;$i<count($this->_visitor_list);$i++){
			$ret[] = clone $this->_visitor_list[$i]["visitor"];
		}
		return $this->_visitor_list;
	}

	function getVisitor($type){
		for($i=0;$i<count($this->_visitor_list);$i++){
			if($this->_visitor_list[$i]["name"] == $type){
				return clone $this->_visitor_list[$i]["visitor"];
			}
		}
		return false;
	}

}

?>