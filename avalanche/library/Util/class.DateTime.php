<?

/**
 * represents a simple Html Document
 */
class DateTime{
	private $year;
	private $month;
	private $day;
	private $hour;
	private $minute;
	private $sec;

	// accepts a hex color (with or without #)
	function __construct($d){
		if(!is_string($d) || !DateTime::isDateTime($d)){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be a datetime formatted string. given: $d");
		}
		$this->year  = substr($d, 0, 4);
		$this->month = substr($d, 5, 2);
		$this->day   = substr($d, 8, 2);
		$this->hour  = substr($d, 11, 2);
		$this->minute = substr($d, 14, 2);
		$this->sec   = substr($d, 17, 2);
	}

	public function year($a = false){
		if(is_int($a)){
			$this->year = $a;
		}else if($a !== false){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		return (int)$this->year;
	}

	public function month($a = false){
		if(is_int($a)){
			$this->month = $a;
		}else if($a !== false){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		return (int)$this->month;
	}

	public function day($a = false){
		if(is_int($a)){
			$this->day = $a;
		}else if($a !== false){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		return (int)$this->day;
	}

	public function hour($a = false){
		if(is_int($a)){
			$this->hour = $a;
		}else if($a !== false){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		return (int)$this->hour;
	}

	public function minute($a = false){
		if(is_int($a)){
			$this->minute = $a;
		}else if($a !== false){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		return (int)$this->minute;
	}

	public function second($a = false){
		if(is_int($a)){
			$this->sec = $a;
		}else if($a !== false){
			throw new IllegalArgumentException("argument to " . __METHOD__ . " must be an int");
		}
		return (int)$this->sec;
	}

	public function getTimeStamp(){
		return mktime($this->hour, $this->minute, $this->sec, $this->month, $this->day, $this->year);
	}


	// returns true if the input matches "YYYY-MM-DD HH:MM:SS" format
	public static function isDateTime($var){
		return preg_match("/^(19|20)\d\d-(0[0-9]|1[0-2])-([0-2][0-9]|3[01]) ([01][0-9]|2[0-4]):[0-5][0-9]:[0-5][0-9]\$/", $var);
	}

	public function __toString() {
		return date("Y-m-d H:i:s", $this->getTimeStamp());
	}

	public function toString() {
		return $this->__toString();
	}



	/********************************
		timezone adjust functions
	********************************/
	// puts this timezone into effect
	// from GMT to GMT + timezone
	// takes daylight savings into
	// account
	public function toTimezone($timezone){
		$hour_offset = floor($timezone);
		$min_offset = (int)(($timezone - $hour_offset) * 60);

		$dst = (int) @date("I", $this->getTimeStamp());
		$hour_offset += $dst;

		$stamp = mktime($this->hour + $hour_offset,
						$this->minute + $min_offset,
						$this->sec,
						$this->month,
						$this->day,
						$this->year);
		$this->year  = @date("Y", $stamp);
		$this->month = @date("m", $stamp);
		$this->day   = @date("d", $stamp);

		$this->hour   = @date("H", $stamp);
		$this->minute = @date("i", $stamp);
		$this->second = @date("s", $stamp);
	}

	// takes this timezone out of effect
	// from GMT + timezone to GMT
	// takes daylight savings into
	// account
	public function toGMT($timezone){
		$hour_offset = floor($timezone);
		$min_offset = (int)(($timezone - $hour_offset) * 60);

		$dst = (int) @date("I", $this->getTimeStamp());
		$hour_offset += $dst;

		$stamp = mktime($this->hour - $hour_offset,
						$this->minute - $min_offset,
						$this->sec,
						$this->month,
						$this->day,
						$this->year);
		$this->year  = @date("Y", $stamp);
		$this->month = @date("m", $stamp);
		$this->day   = @date("d", $stamp);

		$this->hour   = @date("H", $stamp);
		$this->minute = @date("i", $stamp);
		$this->second = @date("s", $stamp);
	}
}

?>