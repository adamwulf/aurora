<?
/********************************************************************************
 *										*
 * event_desc.php								*
 *										*
 ********************************************************************************
 *										*
 * DESCRIPTION									*
 *										*
 * this box shows a description of an event.					*
 *										*
 ********************************************************************************
 *										*
 * FORM INPUT									*
 *										*
 * a calendar id								*
 * an event id is needed. if no event id is given, then this box defaults	*
 * to show the next event in time.						*
 *										*
 * INPUT VARIABLES								*
 *										*
 * $cal_id - the calendar from which to draw the event				*
 * $event_id - the event to load, or null for default				*
 *										*
 ********************************************************************************
 *										*
 * FORM OUTPUT									*
 *										*
 * no form output								*
 * 										*
 ********************************************************************************
 *										*
 * LINKS									*
 *										*
 * 										*
 * conflict.php	- shows a pop-up window describing conflicts with this event.	*
 *			target=_new						*
 *										*
 ********************************************************************************/



include "../../../../include.avalanche.fullApp.php";



$tooltip_on = !$_COOKIE['cook_tooltip_off'];

$my_id = $avalanche->loggedInHuh();

$use_skin = $_COOKIE['cook_skin'];

if(!$use_skin){
	$use_skin = "glass";
}
$skin = $avalanche->getSkin($use_skin);
$buffer = $avalanche->getSkin("buffer");

if(!is_object($skin)){
	$skin = $avalanche->getSkin("glass");
}
$skin->setLayer("side_window");
$fHeight = 250 - $skin->tableHeight() -8; // 8 for buffer
$fWidth = 160;

$skin->setLayer("no_graphic");
$strongcal = $avalanche->getModule("strongcal");

$icon_w = 24;
$top_height = 200;
$max_title_len = 23;
$max_default_count = 5;
if($cal_id && $event_id){

	//$cal_id - the calendar's id
	//$cal_obj - the calendar object
	//$event_id - the event's id
	//$event_obj - the event object
	//$event_title - the event's title
	//$event_st - the start time
	//$event_et - the end time
	//$event_sd - the start date
	//$event_ed - the end date
	//$event_desc - the description
	//$event_color - the event's color
	//$event_author - the event's author

	if(!$external){
		$cal_obj = $strongcal->getCalendarFromDb($cal_id);
	}else{
		$cal_obj = $strongcal->getExternalCalendar($cal_id, $event_date, $event_date);
	}
	$event_obj = $cal_obj->getEvent($event_id);

	$val = $event_obj->getValue($field);
	$field_obj = $event_obj->calendar();
	$field_obj = $field_obj->getField($field);


	$strpos = strpos($val, "\n");
	$overwrite = substr($val, 0, $strpos);
	$val = substr($val, $strpos+1);

	$strpos = strpos($val, "\n");
	$filename = substr($val, 0, $strpos);
	$val = substr($val, $strpos+1);

	$strpos = strpos($val, "\n");
	$mime_type = substr($val, 0, $strpos);
	$val = substr($val, $strpos+1);

	$strpos = strpos($val, "\n");
	$size = substr($val, 0, $strpos);
	$val = substr($val, $strpos+1);


	$width=400;
	$height=300;
	$vid_path = "getvideo.php?cal_id=" . $cal_id . "&event_id=$event_id&field=" . $field;
	$vid_id = $filename;

	$auto = "true";
	if(!is_integer(strrpos($vid_id, ".wmv"))){
		$ret =  "<OBJECT classid='clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B'	";
	        $ret .=	"	codebase='http://www.apple.com/qtactivex/qtplugin.cab'	";
        	$ret .=	"	width='$width' height='$height' 			";
		$ret .=	"	autostart='$auto'>					";
		$ret .=	"	<PARAM name='src' value='$vid_path'>			";
		$ret .= "	<PARAM name='controller' value='false'>			";
		$ret .=	"	<EMBED width='$width' height='$height' 			";
		$ret .=	"	autostart='$auto'					";
		$ret .=	"	src='$vid_path' 					";
		$ret .=	"	name='$vid_id' 						";
		$ret .= "	name='controller' value='false'				";
		$ret .=	"	enablejavascript='true'> 				";
		$ret .= "	<img dynsrc='$vid_path' width='$width' hieght='$height'>";
		$ret .=	"	</img>							";
		$ret .=	"	</EMBED> 						";
		$ret .=	"	</OBJECT> 						";
	}else{
		$ret .=	"	<EMBED width='$width' height='$height' 			";
		$ret .=	"	autostart='$auto'					";
		$ret .=	"	src='$vid_path' 					";
		$ret .= "	name='controller' value='false'				";
		$ret .=	"	enablejavascript='true'> 				";
		$ret .=	"	</EMBED> 						";
	}


}else{
}


echo $ret;
?>