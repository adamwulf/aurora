<?

/**
 * when this action is fired, it will filter the dropdown parameter1 to show only values
 * that have the value of input two in their name
 */
class FilterDropDownAction extends NonKeyAction{
	private $e;
	private $i;
	private $key;
	public function __construct(DropDownInput $e, DropDownInput $key, Input $i){
		$this->i = $i;
		$this->e = $e;
		$this->key = $key;
	}
	
	public function toJS(){
		$id = $this->e->getId();
		$keyid = $this->key->getId();
		$options = "xGetElementById(\"$id\").options";
		$len = "xGetElementById(\"$id\").options.length";
		$keylen = "xGetElementById(\"$keyid\").options.length";
		$value = "xGetElementById(\"" . $this->i->getId() . "\").value.toLowerCase()";
		$index = "xGetElementById(\"$id\").selectedIndex";
		$actions = "var str_$id;
			    document.drop = true;
			    xGetElementById(\"$id\").options.length=0;
			    var index=0;
			    document.drop = false;
			    for(i_$id = 0; i_$id<$keylen && !document.drop;i_$id++){
				str_$id=xGetElementById(\"$keyid\").options[i_$id].text.toLowerCase();
				if(!document.drop && ($value.length == 0 || xNum(str_$id.indexOf($value)) && str_$id.indexOf($value) >= 0)){
					xGetElementById(\"$id\").options[index] = new Option(xGetElementById(\"$keyid\").options[i_$id].text, xGetElementById(\"$keyid\").options[i_$id].value);
					index++;
				}
			    }
			    if(index > 0){
				    xGetElementById(\"$id\").selectedIndex = 0;
			    }"; 
		return $actions;
	}
}


?>