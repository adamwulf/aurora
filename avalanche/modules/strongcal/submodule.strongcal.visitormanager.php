<?
//be sure to leave no white space before and after the php portion of this file
//headers must remain open after this file is included


//////////////////////////////////////////////////////////////////////////
//									//
//  module.strongcal.fields.php						//
//----------------------------------------------------------------------//
//  sub class for the strongcal module. this class represents a field	//
//  in a calendar.							//
//									//
//									//
//									//
//////////////////////////////////////////////////////////////////////////
//									//
//  module.strongcal.fields.php						//
//----------------------------------------------------------------------//
//									//
//  NOTE: ALL MODULES WILL BE INCLUDE *INSIDE* OF THE avalanche'S MAIN	//
//	"CLASS. SO REFER ANY FUNCTION CALLS THAT ARE *OUTSIDE* OF YOUR	//
//	CLASS TO avalanche BY USING *THIS->functionhere*		//
//									//
//////////////////////////////////////////////////////////////////////////


//////////////////////////////////////////////////////////////////
	$realDir = ROOT . APPPATH . MODULES . "strongcal/visitors/";
	$theList = array();
	if ($handle = opendir($realDir)) {
	    while (false != ($file = readdir($handle))) {
        	if ($file != "." && $file != "..") {
			if(is_dir($realDir . $file)){
				if(file_exists($realDir . $file . "/module.strongcal.visitor." . $file . ".php")){
					$inc_file = $realDir . $file . "/module.strongcal.visitor." . $file . ".php";
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
///////////////  STRONGCAL SUB-MODULE   //////////////////////////
///////////////         field           //////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
// this field class defines a field that knows its home calendar//
// and own id. when updated, it will update mysql.		//
//////////////////////////////////////////////////////////////////
// THIS CLASS NEEDS UPDATING SO THAT MYSQL WILL UPDATE		//
// APPROPRIATELY WHEN THE FIELD IS RESET.			//
//////////////////////////////////////////////////////////////////
class module_strongcal_visitormanager {

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
	private $avalanche;
	function __construct($avalanche){
	$this->avalanche = $avalanche;
	$realDir = $this->avalanche->ROOT() . $this->avalanche->APPPATH() . $this->avalanche->MODULES() . "strongcal/visitors/";
	$theList = array();
	if ($handle = opendir($realDir)) {
	    while (false != ($file = readdir($handle))) { 
        	if ($file != "." && $file != "..") { 
			if(is_dir($realDir . $file)){
				if(file_exists($realDir . $file . "/module.strongcal.visitor." . $file . ".php")){
					$obj_class = "module_strongcal_visitor_" . $file;
					$obj = new $obj_class($this->avalanche);
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