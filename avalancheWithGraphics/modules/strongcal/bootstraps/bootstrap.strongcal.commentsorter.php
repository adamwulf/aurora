<?

class module_bootstrap_strongcal_commentsorter extends module_bootstrap_module{

	private $allowed_loaders;

	private $start_date;
	private $end_date;

	function __construct(){
		$this->setName("Aurora Comment List Sorter");
		$this->setInfo("the constructor for this module takes no input. 

                                this module will return a sorted array of comments. it uses the StrongcalCommentComparator to sort.
				the input must be an array of comment objects to sort.
				
				this module throws exceptions for poorly formated input");
	}

	function run($data = false){
		global $avalanche;
		$strongcal = $avalanche->getModule("strongcal");

		if(!($data instanceof module_bootstrap_data)){
			throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be of type module_bootstrap_data.<br>");
		}else
		if(is_array($data->data())){
			$comment_list = $data->data();
			for($i=0;$i<count($comment_list);$i++){
				if(!is_array($comment_list[$i])){
					throw new module_bootstrap_exception("input to method run in " . $this->name() . " must be an array of comment objects.<br>");
				}
			}

			$sorter = new MDASorter();
			$comp   = new StrongcalCommentComparator();
			$sorted_list = $sorter->sort($data->data(), $comp);

			return new module_bootstrap_data($sorted_list, "a sorted list of comments");
		}else{
		  throw new module_bootstrap_exception("input to method run() in " . $this->name() . " must be an array of comment objects");
		}
	}
}
?>