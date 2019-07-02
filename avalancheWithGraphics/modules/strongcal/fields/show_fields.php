<?
/********************************************************************************
 *										*
 * main.php									*
 *										*
 ********************************************************************************
 *										*
 * DESCRIPTION									*
 *										*
 * the main gui of strongcal. default loads the month view target=main_frame	*
 * calendar list 					   target=cal_frame	*
 * and the a blank event_box.php 			   target=event_frame	*
 *										*
 ********************************************************************************
 *										*
 * FORM INPUT									*
 *										*
 * does not take any form input							*
 *										*
 ********************************************************************************
 *										*
 * FORM OUTPUT									*
 *										*
 * a submit button for calendar_list.php					*
 *										*
 ********************************************************************************
 *										*
 * LINKS									*
 *										*
 * next - sends to next.php			target=main_frame		*
 * prev - sends to prev.php			target=main_frame		*
 * day  - sends to day_view.php?date=$date	target=main_frame		*
 * week - sends to week_view.php?date=$date	target=main_frame		*
 * month - sends to month_view.php?date=$date	target=main_frame		*
 * year - sends to month_view.php?date=$date	target=main_frame		*
 * add event - sends to add_event.php. 		target=event_frame		*
 * control panel - sends to control.php 	target=main_frame		*
 *										*
 ********************************************************************************/

include "../../../../include.avalanche.fullApp.php";


$strongcal = $avalanche->getModule("strongcal");
$buffer = $avalanche->getSkin("buffer");
$skin = $avalanche->getSkin("buffer");

$fm = $strongcal->fieldManager();



$field_list = $fm->getFields();
$to_echo = "";
for($i=0;$i<count($field_list);$i++){
	if($field_list[$i]["name"] == "date" ||
	   $field_list[$i]["name"] == "select" ||
	   $field_list[$i]["name"] == "multiselect"){
		$field = $field_list[$i]["field"];
//		$field->_style = true;
		$to_echo .= $skin->tr($skin->td($buffer->table($buffer->tr($buffer->td($field_list[$i]["name"])), "cellpadding='10'")) 
		. $skin->td($buffer->table($buffer->tr($buffer->td($field->toHtml("prefix_", $skin))))));
	}
//		$field->_style = false;
	$to_echo .= $skin->tr($skin->td($buffer->table($buffer->tr($buffer->td($field_list[$i]["name"])), "cellpadding='10'")) 
	. $skin->td($buffer->table($buffer->tr($buffer->td($field_list[$i]["field"]->toHtml("prefix_", $skin))))));
	
}


echo "<html><body bgcolor='" . $skin->bgcolor() . "'>";
echo $skin->table($to_echo, "cellpadding='10' border='0'");
echo "</body></html>";
?>
