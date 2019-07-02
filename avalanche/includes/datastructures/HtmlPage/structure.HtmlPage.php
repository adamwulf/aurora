<?

/**
 * represents a simple Html Document
 */
class HtmlPage extends Document{

	private $body = "";
	private $css = "";

	function text($text){
		$this->body .= $text . "<br><br>";
	}

	function header($header){
		$this->body .= "<h1>" . $header . "</h1>";
		$this->body .= "<hr>";
	}

	function css($css_file){
		$this->css .= "<link rel='STYLESHEET' href='" . $css_file . "' type='text/css'>";
	}
	
	function __toString(){
		$doc  = "<html>";
		$doc .= "<head>";
		$doc .= "<title>" . $this->getTitle() . "</title>";
		$doc .= $this->css;
		$doc .= "</head>";
		$doc .= "<body>";
		$doc .= $this->body;
		$doc .= "</body>";
		$doc .= "</html>";
		return $doc;
	}

}

?>