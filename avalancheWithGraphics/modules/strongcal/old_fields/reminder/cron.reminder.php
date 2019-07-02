<?


$strongcal = $avalanche->getModule("strongcal");

$calendars = $strongcal->getCalendarList();

for($i=0;$i<count($calendars);$i++){
	$gmtime = $strongcal->gmttimestamp();
	$cal = $calendars[$i]["calendar"];
	$cal->start_date(date("Y-m-d", $gmtime));
	$cal->end_date(date("Y-m-d", $gmtime + 60*60*24));
	$cal->reload();
	$events = $cal->events();

	for($j=0;$j<count($events);$j++){
		$fields = $events[$j]->fields();
		for($k=0;$k<count($fields);$k++){
			$event = $events[$j];
			$field = $fields[$k];
			if($field->type() == "reminder"){
				$val = $event->getValue($field->field());
				$vals = explode("\n", $val);
				$time = trim($vals[0]);
				$check = trim($vals[1]);
				$to_email = trim($vals[2]);


				$st = $event->getValue("start_time");
				$sd = $event->getValue("start_date");
				$adjusted = $strongcal->adjust_back($sd, $st);
				$sd = $adjusted["date"];
				$st = $adjusted["time"];

				$year = substr($sd, 0, 4);
				$month = substr($sd, 5, 2);
				$day = substr($sd, 8, 2);
				$hour = substr($st, 0, 2);
				$min = substr($st, 3, 2);

				$min -= $time;

				$stamp = mktime($hour, $min, 0, $month, $day, $year);
				$date = date("Y-m-d H:i", $stamp);
				$now_date = date("Y-m-d H:i", $gmtimestamp);

				if($check && $date == $now_date){
					to_echo("mailing: $to_email");
					mail($to_email, $event->getDisplayValue("title"), $event->getDisplayValue("description"));
				}
			}
		}
	}
}

?>