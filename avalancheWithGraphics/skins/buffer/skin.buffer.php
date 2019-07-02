<?php
//be sure to leave no white space before and after the php portion of this file
//headers must remain open after this file is included


//////////////////////////////////////////////////////////////////////////
//  skin.buffer.php							//
//----------------------------------------------------------------------//
//  initializes the skin's class object and adds it to avalanches	//
//  skin list								//
//									//
//									//
//  NOTE: filename must be of format include.<install folder>.php	//
//////////////////////////////////////////////////////////////////////////



//Syntax - skin classes should always start with skin_ followed by the skin's install folder (name)
class skin_buffer extends skin_template{ 


	//////////////////////////////////////////////////////////////////
	// VARIABLES DEFINED IN TEMPLATE				//
	//--------------------------------------------------------------//
	//	var $name;						//
	//	var $version;						//
	//	var $desc;						//
	//	var $folder;						//
	//								//
	//	var $location;						//
	//	var $level;						//
	//--------------------------------------------------------------//
	// USE FUNCTIONS TO REFERENCE.					//
	//////////////////////////////////////////////////////////////////

	function name() { 
	//////////////////////////////////////////////////////////////////
	//  name()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: string - this skins's name				//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  	returns the name of this object				//
	//////////////////////////////////////////////////////////////////
		return $this->name;
	} 

	function version(){
	//////////////////////////////////////////////////////////////////
	//  version()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: string - this skin's version			//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  	returns the version of this object			//
	//////////////////////////////////////////////////////////////////
		return $this->version;
	}

	function desc(){
	//////////////////////////////////////////////////////////////////
	//  desc()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: string - this skin's description			//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  	returns the description of this object			//
	//////////////////////////////////////////////////////////////////
		return $this->desc;
	}

	function folder(){
	//////////////////////////////////////////////////////////////////
	//  folder()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: string - this skin's install folder			//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  	returns the installation folder of this object		//
	//////////////////////////////////////////////////////////////////
		return $this->folder;
	}

	function init(){
	//////////////////////////////////////////////////////////////////
	//  init()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: none						//
	//								//  
	//  precondition:						//  
	//	should only be called once (at end of this file)	//
	//	(command.php of avalanche will include this		//
	//	   file after installation)				//
	//								//
	//  postcondition:						//
	//  	all variables in this object are initialized		//
	//////////////////////////////////////////////////////////////////
		$this->name = "Buffer Skin";	
		$this->version = "1.0.0";	
		$this->desc = "This is a supplemental skin.";	

		$this->folder = "buffer";
	}

	function skin_buffer(){
		$this->_maxlayer = -1; //max layer allowed (>= $minlayer)
					// init to -1 before adding layers
		$this->_minlayer = 0; //min layer allowed ( > 0)
		$this->_curlayer = $this->_minlayer; //current layer     ($minlayer < $curlayer < $maxlayer)
	}
}

//a temporary instance of this class
//to be added to $allModules after initialization
$SkinBuffer = new skin_buffer;


//initializes object
$SkinBuffer->init();



//////////////////////////////////////////////////////////////////////////////////
//			   BEGIN FIRST LAYER					//

$layer1 = array(
  "a_start" => "<a %extra%>",
  "a_end" => "</a>",
  "background" => "0",// 0 for false or no background
  "bgcolor" => "#FFFFFF",
  "bordercolor" => "#000000",
  "bullet" => "º",
  "button_start" => "<input type='button' %extra%>",
  "button_end" => "",
  "check_start" => "<input type='checkbox' %extra%>",
  "check_end" => "",
  "font_start" => "<font %extra%>",
  "font_end" => "</font>",
  "hr_start" => "<hr width='%width%' %extra%>",
  "hr_end" => "",
  "input_start" => "<input %extra%>",
  "input_end" => "",
  "oli_start" => "<li>",
  "oli_end" => "</li>",
  "ol_start" => "<ol>",
  "ol_end" => "</ol>",
  "option_start" => "<option %extra%>%disp%",
  "option_end" => "</option>",
  "p_start" => "<p %extra%>",
  "p_end" => "</p>",
  "p_title_start" => "<font %extra%>",
  "p_title_end" => "</font>",
  "properties" => "0", 			// 0 for false or no properties
  "radio_start" => "<input type='radio' %extra%>",
  "radio_end" => "",
  "select_start" => "<select %extra%>",
  "select_end" => "</select>",
  "submit_start" => "<input type='submit' %extra%>",
  "submit_end" => "",
  "table_start" => "<table %extra%>",
  "table_end" => "</table>",
  "tdcolor" => "#FFFFFF",
  "td_start" => "<td %extra%>",
  "td_end" => "</td>",
  "thcolor" => "#FFFFFF",
  "th_start" => "<td %extra%>",
  "th_end" => "</td>",
  "textarea_start" => "<textarea %extra%>",
  "textarea_end" => "</textarea>",
  "title_start" => "<font %extra%>",
  "title_end" => "</font>",
  "tr_start" => "<tr %extra%>",
  "tr_end" => "</tr>",
  "ul_start" => "<ul>",
  "ul_end" => "</ul>",
  "uli_start" => "<li>",
  "uli_end" => "</li>"
);

//										//
//////////////////////////////////////////////////////////////////////////////////
//			   ADD THE LAYER TO THE SKIN				//
			$SkinBuffer->addLayer($layer1);
//										//
//////////////////////////////////////////////////////////////////////////////////




//adds module object to avalanche's variable $allModules
$this->addSkin($SkinBuffer);


//be sure to leave no white space before and after the php portion of this file
//headers must remain open after this file is included
?>