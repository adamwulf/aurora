<?
	$done_style = "margin-bottom: 4px; border-bottom: 1px solid black; width: 400px;";
	$start_style = "margin-top: 4px; border-top: 1px solid black; width: 400px;";



	include "../include.avalanche.fullApp.php";
	$bootstrap = $avalanche->getModule("bootstrap");
	$os = $avalanche->getModule("os");
	$strongcal = $avalanche->getModule("strongcal");

	exit;

	$sql = "SELECT * FROM " . $avalanche->PREFIX() . "strongcal_calendars";
	$result = $avalanche->mysql_query($sql);
	$cal_ids = array();
	echo "<div style='$start_style'>&nbsp;&nbsp;looking for calendars<br></div>";
	while($myrow = mysql_fetch_array($result)){
		$cal_ids[] = (int)$myrow["id"];
		echo "&nbsp;&nbsp;&nbsp;&nbsp;finding calendar: " . $myrow["id"] . "<br>";
	}
	echo "<div style='$done_style'>&nbsp;&nbsp; done.<br></div>";

	$event_ids = array();
	echo "<div style='$start_style'>&nbsp;&nbsp;finding events with comments<br></div>";
	foreach($cal_ids as $cal_id){
		$update_sql = "ALTER TABLE `" . $avalanche->PREFIX() . "strongcal_cal_" . $cal_id . "` ADD `has_comments` MEDIUMINT NOT NULL AFTER `all_day` ;";
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;updating calendar to track events<br>";
		$avalanche->mysql_query($update_sql);
		$event_ids[$cal_id] = array();
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;finding events with comments in calendar " . $cal_id . "<br>";
		$sql = "SELECT * FROM " . $avalanche->PREFIX() . "strongcal_cal_" . $cal_id . "_comments GROUP BY event_id";
		$result = $avalanche->mysql_query($sql);
		while($myrow = mysql_fetch_array($result)){
			$test_id = $myrow["event_id"];
			if(!in_array($test_id, $event_ids[$cal_id])){
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;found event with comments: " . $test_id . "<br>";
				$event_ids[$cal_id][] = $test_id;
			}
		}
	}
	echo "<div style='$done_style'>&nbsp;&nbsp; done.<br></div>";

	echo "<div style='$start_style'>&nbsp;&nbsp;finding count for comments per event<br></div>";
	foreach($cal_ids as $cal_id){
		foreach($event_ids[$cal_id] as $event_id){
			$sql = "SELECT COUNT(*) AS count FROM " . $avalanche->PREFIX() . "strongcal_cal_" . $cal_id . "_comments WHERE event_id='" . $event_id . "'";
			$result = $avalanche->mysql_query($sql);
			$myrow = mysql_fetch_array($result);
			$count = $myrow["count"];
			$new_sql = "UPDATE " . $avalanche->PREFIX() . "strongcal_cal_" . $cal_id . " SET has_comments='" . $count . "' WHERE id='" . $event_id . "'";
			echo $new_sql . "<br>";
			$avalanche->mysql_query($new_sql);
		}
	}
	echo "<div style='$done_style'>&nbsp;&nbsp; done.<br></div>";

?>
