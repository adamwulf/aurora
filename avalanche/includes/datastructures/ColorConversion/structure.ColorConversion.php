<?
	class ColorConversion{

		public function figureColor($dark, $light, $count){
			$dark = substr($dark, 1, 6);
			$light = substr($light, 1, 6);
		
			$offset = array();
			for($i = 0; $i <6; $i+=2){
				$offset[] = $this->getOffset(substr($dark, $i, 2), substr($light, $i, 2), $count);
			}
		
			$newColor = implode("", $offset);
			$newColor = "#" . $newColor;
		
			return $newColor;
		}
		
		private function getOffSet($dark_in, $light_in, $count){
			$lighterAmount = 16;
		
			$dark = array();
			$dark[] = $this->hexToInt(substr($dark_in,0,1));
			$dark[] = $this->hexToInt(substr($dark_in,1,1));
		
			$light = array();
			$light[] = $this->hexToInt(substr($light_in,0,1));
			$light[] = $this->hexToInt(substr($light_in,1,1));
		
			$dark[0] *= 16;
			$light[0] *= 16;
		
			$dark = $dark[0] + $dark[1];
			$light = $light[0] + $light[1];
		
		
		
			$new = $light;
			if($count > $lighterAmount){
				$count = $lighterAmount;
			}
		
			while($new == $light && $dark != $light && $lighterAmount > 1 && $count > 0){
				$diff = $light - $dark;
				$new = $light - ($diff*$count)/$lighterAmount;
				$new = intval($new);
				$lighterAmount--;
			}
		/*
 		*	$new = $light - $dark - $lighterAmount;
 		*	$new = $new * $count/3;
 		*	$new = intval($new);
 		*/
		
			if($new < 0){
				$new = 0;
			}
			if($new > 255){
				$new = 255;
			}
		
		//	$new = $light - $new;
		
			$new = $this->intToHex($new);
		
			return $new;
		}
		
		private function hexToInt($ret){
			if(strtoupper($ret) == "F"){
				$ret = 15;
			}else
			if(strtoupper($ret) == "E"){
				$ret = 14;
			}else
			if(strtoupper($ret) == "D"){
				$ret = 13;
			}else
			if(strtoupper($ret) == "C"){
				$ret = 12;
			}else
			if(strtoupper($ret) == "B"){
				$ret = 11;
			}else
			if(strtoupper($ret) == "A"){
				$ret = 10;
			}
		
			return $ret;
		}
		
		private function intToHex($ret){
		
			$hex = array();
			$one = $ret % 16;
			$ret -= $one;
			$hex[] = $ret / 16;
			$hex[] = $one;
		
			if($hex[0] < 0){
				$hex[0] = 0;
			}
			if($hex[1] < 0){
				$hex[1] = 0;
			}
		
		
			for($i = 0; $i < 2; $i++){
				if($hex[$i] == 15){
					$hex[$i] = "F";
				}else
				if($hex[$i] == 14){
					$hex[$i] = "E";
				}else
				if($hex[$i] == 13){
					$hex[$i] = "D";
				}else
				if($hex[$i] == 12){
					$hex[$i] = "C";
				}else
				if($hex[$i] == 11){
					$hex[$i] = "B";
				}else
				if($hex[$i] == 10){
					$hex[$i] = "A";
				}
			}
		
			$hex = implode("", $hex);
		
			return $hex;
		}
	}

?>