<?php
//be sure to leave no white space before and after the php portion of this file
//headers must remain open after this file is included


//////////////////////////////////////////////////////////////////////////
//  skin.template.php							//
//----------------------------------------------------------------------//
//  initializes the skin's class object and adds it to avalanches	//
//  skin list								//
//									//
//									//
//  NOTE: filename must be of format skin.<install folder>.php		//
//////////////////////////////////////////////////////////////////////////



//Syntax - skin classes should always start with skin_ followed by the skin's install folder (name)
class skin_template { 
	protected $_name;
	protected $_version;
	protected $_desc;
	protected $_folder;

	protected $_maxlayer; //max layer allowed (>= $minlayer)
	protected $_minlayer; //min layer allowed ( > 0)
	protected $_curlayer; //current layer     ($minlayer < $curlayer < $maxlayer)

	protected $_layers; // - an array of layers
	protected $_str_layers; // - an array of layers

	function header(){
		return "<link rel='STYLESHEET' href='". HOSTURL . APPPATH . SKINS . $this->folder . "/skin." . $this->folder() . ".style.css' type='text/css'>\n";
	}

	function javascript(){
		return "<script src='". HOSTURL . APPPATH . SKINS . $this->folder() . "/skin." . $this->folder() . ".java.js'></script>\n";
	}

	function more_icon(){
		return HOSTURL . APPPATH . SKINS . "template/images/more.gif";
	}

	function less_icon(){
		return HOSTURL . APPPATH . SKINS . "template/images/less.gif";
	}

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
		return $this->_name;
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
		return $this->_version;
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
		return $this->_desc;
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
		return $this->_folder;
	}

	function __construct(){
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
		$this->_name = "Default Skin";	
		$this->_version = "1.0.0";	
		$this->_desc = "This is the default skin for avalanche.";	

		$this->_folder = "default";

		$this->_layers = array();
		$this->_str_layers = array();
		$this->_maxlayer = 0;
		$this->_minlayer = 0;
		$this->_curlayer = $this->_minlayer;
	}

	// runs the visitor on skin case
	function execute($visitor){
		return $visitor->visit($this);
	}


	//////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////
	//								//
	//	BELOW ARE FUNCTIONS THAT MUST BE INCLUDE WITH ALL SKINS	//
	//	BEGIN LAYER FUNCTIONS					//
	//								//
	//////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////

	function addLayer($layer){
	//////////////////////////////////////////////////////////////////
	//  addLayer()							//
	//--------------------------------------------------------------//
	//  input: $layer - the layer to add				//
	//								//  
	//  output: none						//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  	layer is added to skin 					//
	//////////////////////////////////////////////////////////////////
		$this->_maxlayer = $this->_maxlayer + 1;
		$this->_layers[] = $layer;
	}

	function addNamedLayer($layer, $name){
	//////////////////////////////////////////////////////////////////
	//  addLayer()							//
	//--------------------------------------------------------------//
	//  input: $layer - the layer to add				//
	//  input: $name - the string index to add at			//
	//								//  
	//  output: none						//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  	layer is added to skin 					//
	//////////////////////////////////////////////////////////////////
		$this->_maxlayer = $this->_maxlayer + 1;
		$this->_str_layers[$name] = $layer;
	}


	function setLayer($layer){
	//////////////////////////////////////////////////////////////////
	//  setLayer()							//
	//--------------------------------------------------------------//
	//  input: $layer - the layer to set to				//
	//								//  
	//  output: none						//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  	layer is set	 					//
	//////////////////////////////////////////////////////////////////
		if(is_integer($layer) && $layer >= $this->minLayer() && $layer <= $this->maxLayer()){
			$this->_curlayer=$layer;
			return true;
		}else
		if(is_string($layer)){
			$this->_curlayer=$layer;
			return true;
		}
		return false;
	}


	function getLayer($layer){
	//////////////////////////////////////////////////////////////////
	//  getLayer()							//
	//--------------------------------------------------------------//
	//  input: $layer - the layer to get				//
	//								//  
	//  output: none						//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  	layer "$layer" is returned				//
	//////////////////////////////////////////////////////////////////

		//make sure layer is within bounds, an out of bounds layer will be looped into allowable layer
		// ie: min=4, max = 7, layer = 10 -> layer = 5
		if(is_integer($layer)){
			return $this->_layers[$layer];
		}else
		if(is_string($layer)){
			return $this->_str_layers[$layer];
		}
	}


	function currentLayer(){
	//////////////////////////////////////////////////////////////////
	//  currentLayer()						//
	//--------------------------------------------------------------//
	//  input: none							//
	//								//  
	//  output: returns current layer number			//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
		return $this->_curlayer;
	}

	function minLayer(){
	//////////////////////////////////////////////////////////////////
	//  minLayer()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//								//  
	//  output: returns min layer number				//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////

		return $this->_minlayer;
	}

	function maxLayer(){
	//////////////////////////////////////////////////////////////////
	//  maxLayer()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//								//  
	//  output: returns max layer number				//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////

		return $this->_maxlayer;
	}


	//////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////
	//								//
	//	END LAYER FUNCTIONS					//
	//	BEGIN FORMATTING FUNCTIONS				//
	//								//
	//////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////

	function insertExtra($string, $extra){
	//////////////////////////////////////////////////////////////////
	//  insertExtra()						//
	//--------------------------------------------------------------//
	//  input: $string - the text to search and replace in		//
	//  input: $extra - the text to insert into $string		//
	//  output: formatted $str					//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  	replaces all occurances of "%extra% in $string with	//
	//	the contents of $extra					//
	//////////////////////////////////////////////////////////////////
		if(strpos($string, "%width_only%")){
			$start = strpos($extra, "width=");
			$end = strpos($extra, " ", $start);
			if($end !== false){
				$width = substr($extra, $start, $end - $start + 1);
			}else{
				$width = substr($extra, $start);
			}
			$string = str_replace("%width_only%", $width, $string);
		}
		if(strpos($string, "%height_only%")){
			$start = strpos($extra, "height=");
			$end = strpos($extra, " ", $start);
			if($end !== false){
				$height = substr($extra, $start, $end - $start + 1);
			}else{
				$height = substr($extra, $start);
			}
			$string = str_replace("%height_only%", $height, $string);
		}

		if(strpos($string, "%id_only%")){
			$start = strpos($extra, "id=");
			$end = strpos($extra, " ", $start);
			if($end !== false){
				$id = substr($extra, $start, $end - $start + 1);
			}else{
				$id = substr($extra, $start);
			}
			$string = str_replace("%id_only%", $id, $string);
		}

		if(strpos($string, "%name_only%")){
			$start = strpos($extra, "name=");
			$end = strpos($extra, " ", $start);
			if($end !== false){
				$name = substr($extra, $start, $end - $start + 1);
			}else{
				$name = substr($extra, $start);
			}
			$string = str_replace("%name_only%", $name, $string);
		}

		if(strpos($string, "%align_only%")){
			$start = strpos($extra, "align=");
			$end = strpos($extra, " ", $start);
			if($end !== false){
				$align = substr($extra, $start, $end - $start + 1);
			}else{
				$align = substr($extra, $start);
			}
			$string = str_replace("%align_only%", $align, $string);
		}

		if(strpos($string, "%valign_only%")){
			$start = strpos($extra, "valign=");
			$end = strpos($extra, " ", $start);
			if($end !== false){
				$valign = substr($extra, $start, $end - $start + 1);
			}else{
				$valign = substr($extra, $start);
			}
			$string = str_replace("%valign_only%", $valign, $string);
		}

		if(strpos($string, "%background_only%")){
			$start = strpos($extra, "background=");
			$end = strpos($extra, " ", $start);
			if($end !== false){
				$background = substr($extra, $start, $end - $start + 1);
			}else{
				$background = substr($extra, $start);
			}
			$string = str_replace("%background_only%", $valign, $string);
		}

		return str_replace("%extra%", $extra, $string);
	}


	function insertWidth($string, $width){
	//////////////////////////////////////////////////////////////////
	//  insertWidth()						//
	//--------------------------------------------------------------//
	//  input: $string - the text to search and replace in		//
	//  input: $width - the text to insert into $string		//
	//  output: formatted $str					//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  	replaces all occurances of "%width% in $string with	//
	//	the contents of $width					//
	//////////////////////////////////////////////////////////////////
		return str_replace("%width%", $width, $string);
	}


	function insertDisp($string, $disp){
	//////////////////////////////////////////////////////////////////
	//  insertDisp()						//
	//--------------------------------------------------------------//
	//  input: $string - the text to search and replace in		//
	//  input: $disp - the text to insert into $string		//
	//  output: formatted $str					//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  	replaces all occurances of "%disp% in $string with	//
	//	the contents of $disp					//
	//////////////////////////////////////////////////////////////////
		return str_replace("%disp%", $disp, $string);
	}


	function a($str, $extra = ""){
	//////////////////////////////////////////////////////////////////
	//  a()								//
	//--------------------------------------------------------------//
	//  input: $str - the text to wrap in <a> link style tags	//
	//  input: $extra - extra properties as defined in the readme	//
	//  output: formatted $str					//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  								//
	//////////////////////////////////////////////////////////////////
		$start = "a_start";
		$end = "a_end";

		$layer = $this->getLayer($this->currentLayer());
		$start = $this->insertExtra($layer[$start], $extra);
		$end = $this->insertExtra($layer[$end], $extra);

		$str = $start . $str . $end;
		return $str;
	}


	function background(){
	//////////////////////////////////////////////////////////////////
	//  background()						//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: skin layer's background	'0' for none		//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  								//
	//////////////////////////////////////////////////////////////////
		$layer = $this->getLayer($this->currentLayer());
		return $layer["background"];
	}


	function bgcolor(){
	//////////////////////////////////////////////////////////////////
	//  bgcolor()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: skin layer's bgcolor				//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  								//
	//////////////////////////////////////////////////////////////////
		$layer = $this->getLayer($this->currentLayer());
		return $layer["bgcolor"];
	}


	function bordercolor(){
	//////////////////////////////////////////////////////////////////
	//  bordercolor()						//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: skin layer's border color for tables		//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  								//
	//////////////////////////////////////////////////////////////////
		$layer = $this->getLayer($this->currentLayer());
		return $layer["bordercolor"];
	}


	function bullet(){
	//////////////////////////////////////////////////////////////////
	//  bullet()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: skin layer's bullet					//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  								//
	//////////////////////////////////////////////////////////////////
		$layer = $this->getLayer($this->currentLayer());
		return $layer["bullet"];
	}


	function button($extra){ //extra - name value
	//////////////////////////////////////////////////////////////////
	//  button()							//
	//--------------------------------------------------------------//
	//  input: $extra - extra properties as defined in the readme	//
	//  output: formatted $str					//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  								//
	//////////////////////////////////////////////////////////////////
		$start = "button_start";
		$end = "button_end";

		$layer = $this->getLayer($this->currentLayer());
		$start = $this->insertExtra($layer[$start], $extra);
		$end = $this->insertExtra($layer[$end], $extra);

		$str = $start . $str . $end;
		return $str;
	}


	function check($extra){ //extra - name value
	//////////////////////////////////////////////////////////////////
	//  check()							//
	//--------------------------------------------------------------//
	//  input: $extra - extra properties as defined in the readme	//
	//  output: formatted $str					//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  								//
	//////////////////////////////////////////////////////////////////
		$start = "check_start";
		$end = "check_end";

		$layer = $this->getLayer($this->currentLayer());
		$start = $this->insertExtra($layer[$start], $extra);
		$end = $this->insertExtra($layer[$end], $extra);

		$str = $start . $end;
		return $str;
	}


	function font($str, $extra = ""){
	//////////////////////////////////////////////////////////////////
	//  font()							//
	//--------------------------------------------------------------//
	//  input: $str - the text to wrap in font style tags		//
	//  input: $extra - extra properties as defined in the readme	//
	//  output: formatted $str					//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  								//
	//////////////////////////////////////////////////////////////////
		$start = "font_start";
		$end = "font_end";

		$layer = $this->getLayer($this->currentLayer());
		$start = $this->insertExtra($layer[$start], $extra);
		$end = $this->insertExtra($layer[$end], $extra);

		$str = $start . $str . $end;
		return $str;
	}

	//////////////////////////////////////////////////////////////////
	//  form_style()						//
	//--------------------------------------------------------------//
	//  input: $str - the text to wrap in font style tags		//
	//  input: $extra - extra properties as defined in the readme	//
	//  output: formatted $str					//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  								//
	//////////////////////////////////////////////////////////////////
	function form_style(){
		$color = "form_style";

		$layer = $this->getLayer($this->currentLayer());
		if(isset($layer[$color])){
			$color = $layer[$color];
		}else{
			$color = "";
		}

		return $color;
	}

	//////////////////////////////////////////////////////////////////
	//  form_color()						//
	//--------------------------------------------------------------//
	//  input: $str - the text to wrap in font style tags		//
	//  input: $extra - extra properties as defined in the readme	//
	//  output: formatted $str					//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  								//
	//////////////////////////////////////////////////////////////////
	function form_color(){
		$color = "form_color";

		$layer = $this->getLayer($this->currentLayer());
		if(isset($layer[$color])){
			$color = $layer[$color];
		}else{
			$color = "";
		}

		return $color;
	}

	//////////////////////////////////////////////////////////////////
	//  form_border_style()						//
	//--------------------------------------------------------------//
	//  input: $str - the text to wrap in font style tags		//
	//  input: $extra - extra properties as defined in the readme	//
	//  output: formatted $str					//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  								//
	//////////////////////////////////////////////////////////////////
	function form_border_style(){
		$color = "form_border_style";

		$layer = $this->getLayer($this->currentLayer());
		if(isset($layer[$color])){
			$color = $layer[$color];
		}else{
			$color = "";
		}

		return $color;
	}



	function hr($extra = ""){
	//////////////////////////////////////////////////////////////////
	//  hr()							//
	//--------------------------------------------------------------//
	//  input: $extra - extra properties as defined in the readme	//
	//  input: $width - the width of this hr			//
	//  output: formatted $str					//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  								//
	//////////////////////////////////////////////////////////////////
		$start = "hr_start";
		$end = "hr_end";

		$layer = $this->getLayer($this->currentLayer());
		$start = $this->insertExtra($layer[$start], $extra);
		$end = $this->insertExtra($layer[$end], $extra);

		$str = $start . $str . $end;
		return $str;
	}


	function input($extra){ //extra - name, type, value
	//////////////////////////////////////////////////////////////////
	//  input()							//
	//--------------------------------------------------------------//
	//  input: $extra - extra properties as defined in the readme	//
	//  output: formatted $str					//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  								//
	//////////////////////////////////////////////////////////////////
		$start = "input_start";
		$end = "input_end";

		$layer = $this->getLayer($this->currentLayer());
		$start = $this->insertExtra($layer[$start], $extra);
		$end = $this->insertExtra($layer[$end], $extra);

		$str = $start . $end;
		return $str;
	}


	function oli($str, $extra=""){
	//////////////////////////////////////////////////////////////////
	//  oli()							//
	//--------------------------------------------------------------//
	//  input: $str - text to go inside of an ordered list		//
	//  input: $extra - extra properties as defined in the readme	//
	//  output: formatted $str					//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  								//
	//////////////////////////////////////////////////////////////////
		$start = "oli_start";
		$end = "oli_end";

		$layer = $this->getLayer($this->currentLayer());
		$start = $this->insertExtra($layer[$start], $extra);
		$end = $this->insertExtra($layer[$end], $extra);

		$str = $start . $str . $end;
		return $str;
	}


	function ol($str, $startNum=1, $extra=""){
	//////////////////////////////////////////////////////////////////
	//  ol()							//
	//--------------------------------------------------------------//
	//  input: $str - text to go inside of ordered list		//
	//  input: $extra - extra properties as defined in the readme	//
	//  output: formatted $str					//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  								//
	//////////////////////////////////////////////////////////////////

		$start = "ol_start";
		$end = "ol_end";

		$layer = $this->getLayer($this->currentLayer());
		$start = $this->insertExtra($layer[$start], $extra);
		$end = $this->insertExtra($layer[$end], $extra);

		$str = $start . $str . $end;

		$loc = strpos($str, "%li%");
		while(is_integer($loc)){
			$str = substr_replace($str, $startNum, $loc, 4);
			$startNum++;
			$loc = strpos($str, "%li%");
		}
		return $str;
	}


	function option($value, $disp, $extra=""){ //extra - selected
	//////////////////////////////////////////////////////////////////
	//  option()							//
	//--------------------------------------------------------------//
	//  input: $value - the value for this option			//
	//  input: $disp - the value that should be displayed for this	//
	//		 option's display				//
	//  input: $extra - extra properties as defined in the readme	//
	//  output: formatted $str					//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  								//
	//////////////////////////////////////////////////////////////////
		$start = "option_start";
		$end = "option_end";

		$layer = $this->getLayer($this->currentLayer());
		$start = $this->insertDisp($layer[$start], $disp);
		$end = $this->insertDisp($layer[$end], $disp);

		$extra .= " value='$value'";

		$start = $this->insertExtra($start, $extra);
		$end = $this->insertExtra($end, $extra);

		$str = $start . $end;
		return $str;
	}


	function p($str, $extra = ""){
	//////////////////////////////////////////////////////////////////
	//  p()								//
	//--------------------------------------------------------------//
	//  input: $str - the text to wrap in paragraph style		//
	//		 font tags					//
	//  input: $extra - extra properties as defined in the readme	//
	//  output: formatted $str					//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  								//
	//////////////////////////////////////////////////////////////////
		$start = "p_start";
		$end = "p_end";

		$layer = $this->getLayer($this->currentLayer());
		$start = $this->insertExtra($layer[$start], $extra);
		$end = $this->insertExtra($layer[$end], $extra);

		$str = $start . $str . $end;
		return $str;
	}


	function p_title($str, $extra = ""){
	//////////////////////////////////////////////////////////////////
	//  p_title()							//
	//--------------------------------------------------------------//
	//  input: $str - the text to wrap in paragraph title style	//
	//		 font tags					//
	//  input: $extra - extra properties as defined in the readme	//
	//  output: formatted $str					//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  								//
	//////////////////////////////////////////////////////////////////
		$start = "p_title_start";
		$end = "p_title_end";

		$layer = $this->getLayer($this->currentLayer());
		$start = $this->insertExtra($layer[$start], $extra);
		$end = $this->insertExtra($layer[$end], $extra);

		$str = $start . $str . $end;
		return $str;
	}


	function properties(){
	//////////////////////////////////////////////////////////////////
	//  properties()						//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: skin layer's background properties '0' if none	//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  								//
	//////////////////////////////////////////////////////////////////
		$layer = $this->getLayer($this->currentLayer());
		return $layer["properties"];
	}


	function radio($extra){ //extra - name value
	//////////////////////////////////////////////////////////////////
	//  radio()							//
	//--------------------------------------------------------------//
	//  input: $extra - extra properties as defined in the readme	//
	//  output: formatted $str					//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  								//
	//////////////////////////////////////////////////////////////////
		$start = "radio_start";
		$end = "radio_end";

		$layer = $this->getLayer($this->currentLayer());
		$start = $this->insertExtra($layer[$start], $extra);
		$end = $this->insertExtra($layer[$end], $extra);

		$str = $start . $str . $end;
		return $str;
	}


	function select($str, $extra=""){ //extra - name, size, multiple
	//////////////////////////////////////////////////////////////////
	//  select()							//
	//--------------------------------------------------------------//
	//  input: $extra - extra properties as defined in the readme	//
	//  output: formatted $str					//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  								//
	//////////////////////////////////////////////////////////////////
		$start = "select_start";
		$end = "select_end";

		$layer = $this->getLayer($this->currentLayer());
		$start = $this->insertExtra($layer[$start], $extra);
		$end = $this->insertExtra($layer[$end], $extra);

		$str = $start . $str . $end;
		return $str;
	}


	function submit($extra){ //extra - name value
	//////////////////////////////////////////////////////////////////
	//  submit()							//
	//--------------------------------------------------------------//
	//  input: $extra - extra properties as defined in the readme	//
	//  output: formatted $str					//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  								//
	//////////////////////////////////////////////////////////////////
		$start = "submit_start";
		$end = "submit_end";

		$layer = $this->getLayer($this->currentLayer());
		$start = $this->insertExtra($layer[$start], $extra);
		$end = $this->insertExtra($layer[$end], $extra);

		$str = $start . $end;
		return $str;
	}


	function table($str, $extra = ""){
	//////////////////////////////////////////////////////////////////
	//  table()							//
	//--------------------------------------------------------------//
	//  input: $str - the text to wrap in table style tags		//
	//  input: $width - the width of the table			//
	//  input: $extra - extra properties as defined in the readme	//
	//  output: formatted $str					//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  								//
	//////////////////////////////////////////////////////////////////
		$start = "table_start";
		$end = "table_end";

		$layer = $this->getLayer($this->currentLayer());

		$start = $this->insertExtra($layer[$start], $extra);
		$end = $this->insertExtra($layer[$end], $extra);

		$str = $start . $str . $end;

		return $str;
	}


	function tableHeight(){
	//////////////////////////////////////////////////////////////////
	//  tableHeight()						//
	//--------------------------------------------------------------//
	//  output: formatted added height in pixels to each table	//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  								//
	//////////////////////////////////////////////////////////////////
		$layer = $this->getLayer($this->currentLayer());
		return $layer["table_height"];
	}


	function tableWidth(){
	//////////////////////////////////////////////////////////////////
	//  tableHeight()						//
	//--------------------------------------------------------------//
	//  output: formatted added height in pixels to each table	//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  								//
	//////////////////////////////////////////////////////////////////
		$layer = $this->getLayer($this->currentLayer());
		return $layer["table_width"];
	}


	function td($str, $extra = ""){
	//////////////////////////////////////////////////////////////////
	//  td()							//
	//--------------------------------------------------------------//
	//  input: $str - the text to wrap in td style tags		//
	//  input: $width - the width of the cell			//
	//  input: $extra - extra properties as defined in the readme	//
	//  output: formatted $str					//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  								//
	//////////////////////////////////////////////////////////////////
		$start = "td_start";
		$end = "td_end";

		$layer = $this->getLayer($this->currentLayer());
		$start = $this->insertExtra($layer[$start], $extra);
		$end = $this->insertExtra($layer[$end], $extra);

		$str = $start . $str . $end;
		return $str;
	}


	function tdcolor(){
	//////////////////////////////////////////////////////////////////
	//  tdcolor()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: bgcolor of td's					//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  								//
	//////////////////////////////////////////////////////////////////
		$layer = $this->getLayer($this->currentLayer());
		return $layer["tdcolor"];
	}


	function th($str, $extra = ""){
	//////////////////////////////////////////////////////////////////
	//  th()							//
	//--------------------------------------------------------------//
	//  input: $str - the text to wrap in th style tags		//
	//  input: $width - the width of the cell			//
	//  input: $extra - extra properties as defined in the readme	//
	//  output: formatted $str					//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  								//
	//////////////////////////////////////////////////////////////////
		$start = "th_start";
		$end = "th_end";

		$layer = $this->getLayer($this->currentLayer());
		$start = $this->insertExtra($layer[$start], $extra);
		$end = $this->insertExtra($layer[$end], $extra);

		$str = $start . $str . $end;
		return $str;
	}

	function thcolor(){
	//////////////////////////////////////////////////////////////////
	//  thcolor()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: bgcolor of th's					//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  								//
	//////////////////////////////////////////////////////////////////
		$layer = $this->getLayer($this->currentLayer());
		return $layer["thcolor"];
	}





	function text($str, $size){ //size - the html font size
	//////////////////////////////////////////////////////////////////
	//  text()							//
	//--------------------------------------------------------------//
	//  input: $str - text to go inside of a simple font tag	//
	//  input: $size - the html font size			 	//
	//  output: formatted $str					//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  								//
	//////////////////////////////////////////////////////////////////
		$start = "<font size='$size'>";
		$end = "</font>";

		$str = $start . $str . $end;
		return $str;
	}

	function textarea($str, $extra){ //extra - name, size, multiple
	//////////////////////////////////////////////////////////////////
	//  textarea()							//
	//--------------------------------------------------------------//
	//  input: $str - text to go inside of textarea			//
	//  input: $extra - extra properties as defined in the readme	//
	//  output: formatted $str					//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  								//
	//////////////////////////////////////////////////////////////////
		$start = "textarea_start";
		$end = "textarea_end";

		$layer = $this->getLayer($this->currentLayer());
		$start = $this->insertExtra($layer[$start], $extra);
		$end = $this->insertExtra($layer[$end], $extra);

		$str = $start . $str . $end;
		return $str;
	}


	function title($str, $extra = ""){
	//////////////////////////////////////////////////////////////////
	//  title()							//
	//--------------------------------------------------------------//
	//  input: $str - the text to wrap in title style font tags	//
	//  input: $extra - extra properties as defined in the readme	//
	//  output: formatted $str					//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  								//
	//////////////////////////////////////////////////////////////////
		$start = "title_start";
		$end = "title_end";

		$layer = $this->getLayer($this->currentLayer());
		$start = $this->insertExtra($layer[$start], $extra);
		$end = $this->insertExtra($layer[$end], $extra);

		$str = $start . $str . $end;
		return $str;
	}



	function tr($str, $extra = ""){
	//////////////////////////////////////////////////////////////////
	//  tr()							//
	//--------------------------------------------------------------//
	//  input: $str - the text to wrap in tr style tags		//
	//  input: $extra - extra properties as defined in the readme	//
	//  output: formatted $str					//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  								//
	//////////////////////////////////////////////////////////////////
		$start = "tr_start";
		$end = "tr_end";

		$layer = $this->getLayer($this->currentLayer());
		$start = $this->insertExtra($layer[$start], $extra);
		$end = $this->insertExtra($layer[$end], $extra);

		$str = $start . $str . $end;
		return $str;
	}


	function ul($str, $extra=""){
	//////////////////////////////////////////////////////////////////
	//  ul()							//
	//--------------------------------------------------------------//
	//  input: $str - text to go inside of unordered list		//
	//  input: $extra - extra properties as defined in the readme	//
	//  output: formatted $str					//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  								//
	//////////////////////////////////////////////////////////////////
		$start = "ul_start";
		$end = "ul_end";

		$layer = $this->getLayer($this->currentLayer());
		$start = $this->insertExtra($layer[$start], $extra);
		$end = $this->insertExtra($layer[$end], $extra);

		$str = $start . $str . $end;

		$str = str_replace("%li%", $this->bullet(), $str);

		return $str;
	}

	function uli($str, $extra=""){
	//////////////////////////////////////////////////////////////////
	//  uli()							//
	//--------------------------------------------------------------//
	//  input: $str - text to go inside of unorderd list 		//
	//  input: $extra - extra properties as defined in the readme	//
	//  output: formatted $str					//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//  								//
	//////////////////////////////////////////////////////////////////
		$start = "uli_start";
		$end = "uli_end";

		$layer = $this->getLayer($this->currentLayer());
		$start = $this->insertExtra($layer[$start], $extra);
		$end = $this->insertExtra($layer[$end], $extra);

		$str = $start . $str . $end;
		return $str;
	}
}

//be sure to leave no white space before and after the php portion of this file
//headers must remain open after this file is included
?>