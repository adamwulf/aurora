<?

/**
 * represents an abstract document
 */
class CSS extends Element{

	private $location;

	public function __construct(File $location){
		$this->location = $location;
	}

	/**
	 * sets this paragraph's text
	 */
	public function setLocation(File $location){
		$this->location = $location;
	}

	/**
	 * @return the text of this paragraph
	 */
	public function getLocation(){
		return $this->location;
	}

	public function execute(ElementVisitor $v){
		return $v->accept($this);
	}
}

?>