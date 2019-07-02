<?php
//be sure to leave no white space before and after the php portion of this file
//headers must remain open after this file is included


//////////////////////////////////////////////////////////////////////////
//  skin.default.php							//
//----------------------------------------------------------------------//
//  initializes the skin's class object and adds it to avalanches	//
//  skin list								//
//									//
//									//
//  NOTE: filename must be of format include.<install folder>.php	//
//////////////////////////////////////////////////////////////////////////



//Syntax - skin classes should always start with skin_ followed by the skin's install folder (name)
class skin_default extends skin_template{ 


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
		$this->name = "Default Skin";	
		$this->version = "1.0.0";	
		$this->desc = "This is the default skin for avalanche.";	

		$this->folder = "default";
	}

	function skin_default(){
		$this->_maxlayer = -1; //max layer allowed (>= $minlayer)
					// init to -1 before adding layers
		$this->_minlayer = 0; //min layer allowed ( > 0)
		$this->_curlayer = $this->_minlayer; //current layer     ($minlayer < $curlayer < $maxlayer)
	}
}

//a temporary instance of this class
//to be added to $allModules after initialization
$SkinDefault = new skin_default;


//initializes object
$SkinDefault->init();



//////////////////////////////////////////////////////////////////////////////////
//			   BEGIN FIRST LAYER					//

$layer1 = array(
  "a_start" => "<a %extra%  class='black'>",
  "a_end" => "</a>",
  "background" => "0",// 0 for false or no background
  "bgcolor" => "#FFFFFF",
  "bullet" => "º",
  "button_start" => "<input type='button' %extra% style='background-color: #CCCCCC; color: #000000; font-family: Verdana; font-size: 12px; border: 1 solid #000000'>",
  "button_end" => "",
  "check_start" => "<input type='checkbox' %extra%>",
  "check_end" => "",
  "font_start" => "<font face='verdana' size='2' color='#000000' %extra%>",
  "font_end" => "</font>",
  "hr_start" => "<hr width='%width%' %extra%>",
  "hr_end" => "",
  "input_start" => "<input %extra% style='background-color: #CCCCCC; color: #000000; font-family: Verdana; font-size: 10px; border: 1 solid #000000'>",
  "input_end" => "",
  "oli_start" => "<li>",
  "oli_end" => "</li>",
  "ol_start" => "<ol>",
  "ol_end" => "</ol>",
  "option_start" => "<option %extra%>%disp%",
  "option_end" => "</option>",
  "p_start" => "<p %extra%>",
  "p_end" => "</p>",
  "p_title_start" => "<font size='2' face='verdana' color='#000000'><b>",
  "p_title_end" => "</font>",
  "properties" => "0", 			// 0 for false or no properties
  "radio_start" => "<input type='radio' %extra%>",
  "radio_end" => "",
  "select_start" => "<select %extra%>",
  "select_end" => "</select>",
  "submit_start" => "<input type='submit' %extra% style='background-color: #CCCCCC; color: #000000; font-family: Verdana; font-size: 12px; border: 1 solid #000000'>",
  "submit_end" => "",
  "table_start" => "<table width='%width%' cellpadding='3' cellspacing='0' border='1' %extra% bgcolor='#FFFFFF'>",
  "table_end" => "</table>",
  "tdcolor" => "#FFFFFF",
  "td_start" => "<td %extra%>",
  "td_end" => "</td>",
  "thcolor" => "#CCCCCC",
  "th_start" => "<td %extra% bgcolor='#CCCCCC'>",
  "th_end" => "</td>",
  "textarea_start" => "<textarea %extra% style='background-color: #CCCCCC; color: #000000; font-family: Verdana; font-size: 12px; border: 1 solid #000000'>",
  "textarea_end" => "</textarea>",
  "title_start" => "<font size='3' color='#333333' face='arial' %extra%><b>",
  "title_end" => "</b></font>",
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
			$SkinDefault->addLayer($layer1);
//										//
//////////////////////////////////////////////////////////////////////////////////


//////////////////////////////////////////////////////////////////////////////////
//			   BEGIN FIRST LAYER					//

$layer2 = array(
  "a_start" => "<a %extra%  class='black'>",
  "a_end" => "</a>",
  "background" => "0",// 0 for false or no background
  "bgcolor" => "#FFFFFF",
  "bullet" => "º",
  "button_start" => "<input type='button' %extra% style='background-color: #CCCCCC; color: #000000; font-family: Verdana; font-size: 12px; border: 1 solid #000000'>",
  "button_end" => "",
  "check_start" => "<input type='checkbox' %extra%>",
  "check_end" => "",
  "font_start" => "<font face='verdana' size='2' color='#000000' %extra%>",
  "font_end" => "</font>",
  "hr_start" => "<hr width='%width%' %extra%>",
  "hr_end" => "",
  "input_start" => "<input %extra% style='background-color: #CCCCCC; color: #000000; font-family: Verdana; font-size: 10px; border: 1 solid #000000'>",
  "input_end" => "",
  "oli_start" => "<li>",
  "oli_end" => "</li>",
  "ol_start" => "<ol>",
  "ol_end" => "</ol>",
  "option_start" => "<option %extra%>%disp%",
  "option_end" => "</option>",
  "p_start" => "<p %extra%>",
  "p_end" => "</p>",
  "p_title_start" => "<font size='2' face='verdana' color='#000000'><b>",
  "p_title_end" => "</font>",
  "properties" => "0", 			// 0 for false or no properties
  "radio_start" => "<input type='radio' %extra%>",
  "radio_end" => "",
  "select_start" => "<select %extra%>",
  "select_end" => "</select>",
  "submit_start" => "<input type='submit' %extra% style='background-color: #CCCCCC; color: #000000; font-family: Verdana; font-size: 12px; border: 1 solid #000000'>",
  "submit_end" => "",
  "table_start" => "<table width='%width%' cellpadding='0' cellspacing='0' border='1' %extra% bgcolor='#FFFFFF'>",
  "table_end" => "</table>",
  "tdcolor" => "#FFFFFF",
  "td_start" => "<td %extra%>",
  "td_end" => "</td>",
  "thcolor" => "#CCCCCC",
  "th_start" => "<td %extra% bgcolor='#CCCCCC'>",
  "th_end" => "</td>",
  "textarea_start" => "<textarea %extra% style='background-color: #CCCCCC; color: #000000; font-family: Verdana; font-size: 12px; border: 1 solid #000000'>",
  "textarea_end" => "</textarea>",
  "title_start" => "<font size='3' color='#333333' face='arial' %extra%><b>",
  "title_end" => "</b></font>",
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
			$SkinDefault->addLayer($layer2);
//										//
//////////////////////////////////////////////////////////////////////////////////


//adds module object to avalanche's variable $allModules
$this->addSkin($SkinDefault);


//be sure to leave no white space before and after the php portion of this file
//headers must remain open after this file is included
?>