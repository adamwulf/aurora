<?php
//be sure to leave no white space before and after the php portion of this file
//headers must remain open after this file is included


//////////////////////////////////////////////////////////////////////////
//  skin.installer.php							//
//----------------------------------------------------------------------//
//  initializes the skin's class object and adds it to avalanches	//
//  skin list								//
//									//
//									//
//  NOTE: filename must be of format include.<install folder>.php	//
//////////////////////////////////////////////////////////////////////////


//Syntax - skin classes should always start with skin_ followed by the skin's install folder (name)
class skin_installer extends skin_template{ 


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
		$this->name = "installer Skin";	
		$this->version = "1.0.0";	
		$this->desc = "This is the skin for installer.inversiondesigns.com.";	

		$this->folder = "installer";
	}

	function skin_installer(){
		$this->_maxlayer = -1; //max layer allowed (>= $minlayer)
					// init to -1 before adding layers
		$this->_minlayer = 0; //min layer allowed ( > 0)
		$this->_curlayer = $this->_minlayer; //current layer     ($minlayer < $curlayer < $maxlayer)
	}
}

//a temporary instance of this class
//to be added to $allModules after initialization
$Skininstaller = new skin_installer;


//initializes object
$Skininstaller->init();



//////////////////////////////////////////////////////////////////////////////////
//			   BEGIN FIRST LAYER					//

$layer1 = array(
  "a_start" => "<a %extra% class='black'>",
  "a_end" => "</a>",
  "background" => "0",// 0 for false or no background
  "bgcolor" => "#7A898E",
  "bullet" => "-",
  "button_start" => "<input type='button' %extra% style='background-color: #ffffff; color: #8DA2AA; font-family: Verdana; font-size: 10px; border: 1 solid #8DA2AA'>",
  "button_end" => "",
  "check_start" => "<input type='checkbox' %extra%>",
  "check_end" => "",
  "font_start" => "<font face='verdana' size='2' color='#000000' %extra%>",
  "font_end" => "</font>",
  "hr_start" => "<hr width='%width%' %extra%>",
  "hr_end" => "",
  "input_start" => "<input %extra% style='background-color: #ffffff; color: #000000; font-family: Verdana; font-size: 10px; border: 1 solid #1c4259'>",
  "input_end" => "",
  "oli_start" => ":%li%: ",
  "oli_end" => "<br>",
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
  "select_start" => "<select %extra% style='background-color: #ffffff; color: #000000; font-family: Verdana; font-size: 10px; border: 1 solid #000000'>",
  "select_end" => "</select>",
  "submit_start" => "<input type='submit' %extra% style='background-color: #ffffff; color: #8DA2AA; font-family: Verdana; font-size: 10px border: 1 solid #5B6E75'>",
  "submit_end" => "",
  "table_start" => "<table %extra% style='border-collapse: collapse; border-style: solid; border-width: 3px; border-color: #5B6E75;'>",
  "table_end" => "</table>",
  "tdcolor" => "#8DA2AA",
  "td_start" => "<td %extra% style='border-collapse: collapse; border-style: solid; border-width: 2px; border-color: #5B6E75; ' bgcolor='#8DA2AA'>",
  "td_end" => "</td>",
  "thcolor" => "#ffffff",
  "th_start" => "<td %extra% bgcolor='#ffffff'>",
  "th_end" => "</td>",
  "textarea_start" => "<textarea %extra% style='background-color: #ffffff; color: #000000; font-family: Verdana; font-size: 10px; border: 1 solid #5B6E75'>",
  "textarea_end" => "</textarea>",
  "title_start" => "<font size='1' color='#000000' face='verdana' %extra%><b>",
  "title_end" => "</b></font>",
  "tr_start" => "<tr %extra%>",
  "tr_end" => "</tr>",
  "ul_start" => "<ul>",
  "ul_end" => "</ul>",
  "uli_start" => "%li% ",
  "uli_end" => "<br>"
);

//										//
//////////////////////////////////////////////////////////////////////////////////
//			   ADD THE LAYER TO THE SKIN				//
			$Skininstaller->addLayer($layer1);
//										//
//////////////////////////////////////////////////////////////////////////////////
//			   BEGIN SECOND LAYER					//

$layer2 = array(
    "a_start" => "<a %extra% class='black'><font size='1'>",
  "a_end" => "</font></a>",
  "background" => "0",// 0 for false or no background
  "bgcolor" => "#7A898E",
  "bullet" => "-",
  "button_start" => "<input type='button' %extra% style='background-color: #ffffff; color: #8DA2AA; font-family: Verdana; font-size: 10px; border: 1 solid #8DA2AA'>",
  "button_end" => "",
  "check_start" => "<input type='checkbox' %extra%>",
  "check_end" => "",
  "font_start" => "<font face='verdana' size='2' color='#000000' %extra%>",
  "font_end" => "</font>",
  "hr_start" => "<hr width='%width%' %extra%>",
  "hr_end" => "",
  "input_start" => "<input %extra% style='background-color: #ffffff; color: #000000; font-family: Verdana; font-size: 10px; border: 1 solid #1c4259'>",
  "input_end" => "",
  "oli_start" => ":%li%: ",
  "oli_end" => "<br>",
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
  "select_start" => "<select %extra% style='background-color: #ffffff; color: #000000; font-family: Verdana; font-size: 10px; border: 1 solid #000000'>",
  "select_end" => "</select>",
  "submit_start" => "<input type='submit' %extra% style='background-color: #ffffff; color: #8DA2AA; font-family: Verdana; font-size: 10px border: 1 solid #5B6E75'>",
  "submit_end" => "",
  "table_start" => "<table %extra% style='border-collapse: collapse; border-style: solid; border-width: 3px; border-color: #5B6E75;'>",
  "table_end" => "</table>",
  "tdcolor" => "#ffffff",
  "td_start" => "<td %extra% style='border-collapse: collapse; border-style: solid; border-width: 2px; border-color: #5B6E75; ' bgcolor='#ffffff'>",
  "td_end" => "</td>",
  "thcolor" => "#ffffff",
  "th_start" => "<td %extra% bgcolor='#ffffff'>",
  "th_end" => "</td>",
  "textarea_start" => "<textarea %extra% style='background-color: #ffffff; color: #000000; font-family: Verdana; font-size: 10px; border: 1 solid #5B6E75'>",
  "textarea_end" => "</textarea>",
  "title_start" => "<font size='1' color='#000000' face='verdana' %extra%><b>",
  "title_end" => "</b></font>",
  "tr_start" => "<tr %extra%>",
  "tr_end" => "</tr>",
  "ul_start" => "<ul>",
  "ul_end" => "</ul>",
  "uli_start" => "%li% ",
  "uli_end" => "<br>"
);

//										//
//////////////////////////////////////////////////////////////////////////////////
//			   ADD THE LAYER TO THE SKIN				//
			$Skininstaller->addLayer($layer2);
//										//
//////////////////////////////////////////////////////////////////////////////////

$layer3 = array(
    "a_start" => "<a %extra% class='black'>",
  "a_end" => "</a>",
  "background" => "0",// 0 for false or no background
  "bgcolor" => "#7A898E",
  "bullet" => "-",
  "button_start" => "<input type='button' %extra% style='background-color: #ffffff; color: #8DA2AA; font-family: Verdana; font-size: 10px; border: 1 solid #8DA2AA'>",
  "button_end" => "",
  "check_start" => "<input type='checkbox' %extra%>",
  "check_end" => "",
  "font_start" => "<font face='verdana' size='2' color='#000000' %extra%>",
  "font_end" => "</font>",
  "hr_start" => "<hr width='%width%' %extra%>",
  "hr_end" => "",
  "input_start" => "<input %extra% style='background-color: #ffffff; color: #000000; font-family: Verdana; font-size: 10px; border: 1 solid #1c4259'>",
  "input_end" => "",
  "oli_start" => ":%li%: ",
  "oli_end" => "<br>",
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
  "select_start" => "<select %extra% style='background-color: #ffffff; color: #000000; font-family: Verdana; font-size: 10px; border: 1 solid #000000'>",
  "select_end" => "</select>",
  "submit_start" => "<input type='submit' %extra% style='background-color: #ffffff; color: #8DA2AA; font-family: Verdana; font-size: 10px border: 1 solid #5B6E75'>",
  "submit_end" => "",
  "table_start" => "<table %extra% style='border-collapse: collapse; border-style: solid; border-width: 3px; border-color: #5B6E75;'>",
  "table_end" => "</table>",
  "tdcolor" => "#5B6E75",
  "td_start" => "<td %extra% style='border-collapse: collapse; border-style: solid; border-width: 2px; border-color: #5B6E75; ' bgcolor='#5B6E75'>",
  "td_end" => "</td>",
  "thcolor" => "#ffffff",
  "th_start" => "<td %extra% bgcolor='#ffffff'>",
  "th_end" => "</td>",
  "textarea_start" => "<textarea %extra% style='background-color: #ffffff; color: #000000; font-family: Verdana; font-size: 10px; border: 1 solid #5B6E75'>",
  "textarea_end" => "</textarea>",
  "title_start" => "<font size='1' color='#000000' face='verdana' %extra%><b>",
  "title_end" => "</b></font>",
  "tr_start" => "<tr %extra%>",
  "tr_end" => "</tr>",
  "ul_start" => "<ul>",
  "ul_end" => "</ul>",
  "uli_start" => "%li% ",
  "uli_end" => "<br>"
);

//										//
//////////////////////////////////////////////////////////////////////////////////
//			   ADD THE LAYER TO THE SKIN				//
			$Skininstaller->addLayer($layer3);
//										//
//////////////////////////////////////////////////////////////////////////////////


//adds module object to avalanche's variable $allModules
$this->addSkin($Skininstaller);


//be sure to leave no white space before and after the php portion of this file
//headers must remain open after this file is included
?>
