<?

/**
 * represents an abstract document
 */
class Document extends StyledElement{

	public static $onLoad = "onload";
	public static $onResize = "onresize";

	private $title;

	private $onLoadActions;
	private $onResizeActions;
	private $functions;

	private $elements;

	private $hidden_elements;

	private $css;

	private $js;

	public function __construct($title="No Title"){
		parent::__construct();
		$this->title = $title;
		$this->onLoadActions = array();
		$this->onResizeActions = array();
		$this->elements = array();
		$this->css = array();
		$this->js = array();

		$this->addJS(new File(HOSTURL . APPPATH . JAVASCRIPT . "x/x_load.js"));
		$this->addJS(new File(HOSTURL . APPPATH . JAVASCRIPT . "x/x_core.js"));
		$this->addJS(new File(HOSTURL . APPPATH . JAVASCRIPT . "x/x_event.js"));
		$this->addJS(new File(HOSTURL . APPPATH . JAVASCRIPT . "x/x_timer.js"));
		$this->addJS(new File(HOSTURL . APPPATH . JAVASCRIPT . "x/x_cook.js"));
		$this->addJS(new File(HOSTURL . APPPATH . JAVASCRIPT . "x/menus/xmenu1.js"));
		$this->addJS(new File(HOSTURL . APPPATH . JAVASCRIPT . "x/x_slide.js"));
		$this->addJS(new File(HOSTURL . APPPATH . JAVASCRIPT . "x/x_debug.js"));
		$this->addJS(new File(HOSTURL . APPPATH . JAVASCRIPT . "y/y_core.js"));
		$this->addJS(new File(HOSTURL . APPPATH . JAVASCRIPT . "y/y_tooltipgroup.js"));
		$this->addJS(new File(HOSTURL . APPPATH . JAVASCRIPT . "y/yfilter.js"));
	}

	public function addJS(File $e){
		$this->js[] = $e;
	}

	public function removeJS(File $e){
		$index = array_search($e, $this->js);
		if(isset($this->js[$index])){
			array_splice($this->js, $index, 1);
			return true;
		}else{
			return false;
		}
	}

	public function getJS(){
		return $this->js;
	}

	/**
	 * sets this documents title
	 */
	public function setTitle($title){
		if(is_string($title)){
			$this->title = $title;
		}else{
			throw new IllegalArgumentException("argument \$title must be a string to function Document::setTitle(\$title)");
		}
	}

	/**
	 * @return the title of this document
	 */
	public function getTitle(){
		return $this->title;
	}

	/**
	 * adds a js function to the document
	 */
	public function addFunction(NewFunctionAction $a){
		$this->functions[] = $a;
	}


	/**
	 * removes a js function from a document
	 */
	public function removeFunction(NewFunctionAction $a){
		$index = array_search($a, $this->functions);
		if(isset($this->functions[$index])){
			array_splice($this->functions, $index, 1);
			return true;
		}else{
			return false;
		}
	}

	/**
	 * returns a list of js functions
	 */
	public function getFunctions(){
		return $this->functions;
	}

	/**
	 * adds an action that will be invoked when this document loads
	 */
	public function addAction(NonKeyAction $a, $condition = "onload"){
		if(!in_array($condition, array(Document::$onLoad, Document::$onResize))){
			throw new IllegalArgumentException("Second argument to addAction must be either Document::\$onLoad or Document::\$onResize");
		}
		if($condition == Document::$onLoad){
			$this->onLoadActions[] = $a;
		}else{
			$this->onResizeActions[] = $a;
		}
	}

	/**
	 * removes an Action from this document
	 * @return true if successful, false otherwise
	 */
	public function removeAction(NonKeyAction $a, $condition = "onload"){
		if(!in_array($condition, array(Document::$onLoad, Document::$onResize))){
			throw new IllegalArgumentException("Second argument to addAction must be either Document::\$onLoad or Document::\$onResize");
		}
		if($condition == Document::$onLoad){
			$index = array_search($a, $this->onLoadActions);
			if(isset($this->onLoadActions[$index])){
				array_splice($this->onLoadActions, $index, 1);
				return true;
			}else{
				return false;
			}
		}else{
			$index = array_search($a, $this->onResizeActions);
			if(isset($this->onResizeActions[$index])){
				array_splice($this->onResizeActions, $index, 1);
				return true;
			}else{
				return false;
			}
		}
	}

	/**
	 * returns an array of the actions registered with this document
	 */
	 public function getActions($condition = "onload"){
		if(!in_array($condition, array(Document::$onLoad, Document::$onResize))){
			throw new IllegalArgumentException("First argument to addAction must be either Document::\$onLoad or Document::\$onResize");
		}
		if($condition == Document::$onLoad){
			return $this->onLoadActions;
		}else{
			return $this->onResizeActions;
		}
	 }

	/**
	 * adds an element to this document
	 */
	public function add(Element $e){
		$this->elements[] = $e;
	}


	/**
	 * removes an Element from this document
	 * @return true if successful, false otherwise
	 */
	public function remove(Element $e){
		$index = array_search($e, $this->elements);
		if($index !== false && isset($this->elements[$index])){
			array_splice($this->elements, $index, 1);
			return true;
		}else{
			return false;
		}
	}

	/**
	 * returns an array of all the Elements in this Document
	 */
	public function getElements(){
		return $this->elements;
	}

	/**
	 * adds a hidden element to this document
	 */
	public function addHidden(Element $e){
		$this->hidden_elements[] = $e;
	}


	/**
	 * removes an Element from this document
	 * @return true if successful, false otherwise
	 */
	public function removeHidden(Element $e){
		$index = array_search($e, $this->hidden_elements);
		if($index !== false && isset($this->hidden_elements[$index])){
			array_splice($this->hidden_elements, $index, 1);
			return true;
		}else{
			return false;
		}
	}

	/**
	 * returns an array of all the Elements in this Document
	 */
	public function getHiddenElements(){
		return $this->hidden_elements;
	}

	/**
	 * adds an CSS to this document
	 */
	public function addStyleSheet(CSS $e){
		$this->css[] = $e;
	}


	/**
	 * removes an Element from this document
	 * @return true if successful, false otherwise
	 */
	public function removeStyleSheet(CSS $e){
		$index = array_search($e, $this->css);
		if(isset($this->css[$index])){
			array_splice($this->css, $index, 1);
			return true;
		}else{
			return false;
		}
	}

	/**
	 * returns an array of all the Elements in this Document
	 */
	public function getStyleSheets(){
		return $this->css;
	}
}

?>