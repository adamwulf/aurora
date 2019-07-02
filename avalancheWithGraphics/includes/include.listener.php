<?
//be sure to leave no white space before and after the php portion of this file
//headers must remain open after this file is included


//////////////////////////////////////////////////////////////////////////
//									//
//  include.visitor.php							//
//----------------------------------------------------------------------//
//  this describes the abstract visitor class for an avalanche visitor	//
//									//
//									//
//////////////////////////////////////////////////////////////////////////



//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
///////////////                         //////////////////////////
///////////////    AVALANCHE VISITOR    //////////////////////////
///////////////                         //////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
interface avalanche_listener {
}


abstract class avalanche_listenable {
	private $listeners = array();
	
	public function addListener($list){
		if(!($list instanceof avalanche_listener)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an avalanche_listener");
		}
		if(!array_search($list, $this->listeners)){
			$this->listeners[] = $list;
		}
	}
	
	public function removeListener($list){
		if(!($list instanceof avalanche_listener)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an avalanche_listener");
		}
		$index = array_search($list, $this->listeners);
		if($index !== false){
			array_splice($this->listeners, $index, 1);
		}
	}
	
	public function getListeners(){
		return $this->listeners;
	}
	
}

?>