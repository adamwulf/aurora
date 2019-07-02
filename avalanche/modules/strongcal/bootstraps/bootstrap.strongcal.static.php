<?

class StrongcalGuiHelper {

	public static function eventTitleString($avalanche, $event, $now_date){
		if(!($event instanceof module_strongcal_event)){
			throw new IllegalArgumentException("argument 2 of " . __METHOD__ . " must be a strongcal event");
		}
		$reminders = $avalanche->getModule("reminder");
		$event_id = $event->getId();
		$cal_id = $event->calendar()->getId();

		// fix start time to look nice
		if($now_date == $event->getDisplayValue("start_date")){
			$start_time = $event->getDisplayValue("start_time");
			$start_hour = (int)substr($start_time, 0, 2);
			$start_min  = (int)substr($start_time, 3, 2);
			$am_pm = "a";
			if($start_time >= 12){
				$am_pm = "p";
			}
			if($start_hour == 0){
				$start_hour = 12;
			}
			if($start_hour > 12){
				$start_hour -= 12;
			}
			if($start_min < 10){
				$start_min = "0" . $start_min;
			}
			$start_time = $start_hour . ":" . $start_min . $am_pm;
		}else{
			$start_time = (int)substr($event->getDisplayValue("start_date"), 5, 2) . "/" . (int)substr($event->getDisplayValue("start_date"), 8, 2);
		}

		// make the calendar color cell
		$icon = new Panel();
		$icon->getStyle()->setBackground($event->calendar()->color());
		$icon->getStyle()->setClassname("aurora_view_icon");

		$title = trim($event->getDisplayValue("title"));
		if(strlen($title) == 0){
			$title = "<i>no title</i>";
		}

		// get event title
		if(!$event->isAllDay()){
			$title = $start_time . ": " . $title;
		}else{
			$title = " [" . $title . "]";
		}
		if(strcasecmp($event->getDisplayValue("priority"), "high") == 0){
			$title = "<b>" . $title . "</b>";
		}else if(strcasecmp($event->getDisplayValue("priority"), "low") == 0){
			$title = "<i>" . $title . "</i>";
		}

		$link = new Panel();

		$space_added = false;

		// $text = new Link($title, "javascript:;");
		// $this->createEventMenu($text, $cal_id, $event_id);
		if(count($reminders->getMyRemindersFor($event)) > 0){
			$icon = new Icon($avalanche->HOSTURL() . $avalanche->APPPATH() . $avalanche->MODULES() . "os/images/alarm.gif");
			$icon->setWidth(19);
			$icon->setHeight(10);
			$link->add($icon);
			if(!$space_added){
				$space_added = true;
				$title = "&nbsp;" . $title;
			}
		}

		// comments icon
		if($event->hasComments()){
			$icon = new Icon($avalanche->HOSTURL() . $avalanche->APPPATH() . $avalanche->MODULES() . "strongcal/gui/os/small-bubble.png");
			$icon->setWidth(10);
			$icon->setWidth(10);
			$link->add($icon);
			if(!$space_added){
				$space_added = true;
				$title = "&nbsp;" . $title;
			}
		}

		// create link
		$title = new Link($title, "index.php?primary_loader=module_bootstrap_strongcal_main_loader&view=event&event_id=$event_id&cal_id=$cal_id");

		$link->add($title);
		$link->setStyle(new Style("month_cell_text"));

		return $link;
	}

	public static function createEventTip($avalanche, $event){
		if(!($avalanche instanceof avalanche_class)){
			throw new IllegalArgumentException("argument 2 of " . __METHOD__ . " must be an avalanche object");
		}
		if(!($event instanceof module_strongcal_event)){
			throw new IllegalArgumentException("argument 2 of " . __METHOD__ . " must be a strongcal event");
		}
		$desc = "";
		if($event->isAllDay()){
			$desc = "All Day Event<br>";
		}
		if(strlen(trim($event->getDisplayValue("description")))){
			$d = str_replace("\n","<br>",$event->getDisplayValue("description"));
			$desc .= "Description:<br>" . $d;
			$desc = wordwrap($desc, 35, "<br>");
		}
		if($event->hasComments()){
			if(strlen($desc)) $desc .= "<br>";
			$s = $event->hasComments() != 1 ? "s" : "";
			$desc .= "(" . $event->hasComments() . " comment$s)";
		}
		$desc = new Text($desc);
		return OsGuiHelper::createToolTip($desc);
	}
}



?>
