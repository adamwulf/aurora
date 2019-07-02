<?
//be sure to leave no white space before and after the php portion of this file
//headers must remain open after this file is included


//////////////////////////////////////////////////////////////////////////
//									//
//  module.strongcal.recurrance.php					//
//----------------------------------------------------------------------//
//  sub class for the strongcal module. this class represents a field	//
//  in a calendar.							//
//									//
//									//
//									//
//////////////////////////////////////////////////////////////////////////
//									//
//  module.strongcal.recurrance.php					//
//----------------------------------------------------------------------//
//									//
//  NOTE: ALL MODULES WILL BE INCLUDE *INSIDE* OF THE avalanche'S MAIN	//
//	CLASS. SO REFER ANY FUNCTION CALLS THAT ARE *OUTSIDE* OF YOUR	//
//	CLASS TO avalanche BY USING *THIS->functionhere*		//
//									//
//////////////////////////////////////////////////////////////////////////



//////////////////////////////////////////
// DEFINE CONSTANTS			//
define("RECUR_DAILY",        "1");	//
define("RECUR_WEEKLY",       "2");	//
define("RECUR_MONTHLY",      "3");	//
define("RECUR_YEARLY",       "4");	//
define("RECUR_NO_END_DATE",  "5");	//
define("RECUR_END_AFTER",    "6");	//
define("RECUR_END_BY",       "7");	//
define("RECUR_MONTHLY_DOM",  "8");	//
define("RECUR_MONTHLY_DOW",  "9");	//
define("RECUR_YEARLY_DOM",  "10");	//
define("RECUR_YEARLY_DOW",  "11");	//
define("RECUR_MAX_DATE", "2038-01-19");	//
define("RECUR_MIN_DATE", "1901-12-13");	//
//					//
//////////////////////////////////////////



//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
///////////////                         //////////////////////////
///////////////  STRONGCAL SUB-MODULE   //////////////////////////
///////////////       recurrance        //////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
// defines a recurrance pattern. this object knows its home	//
// calendar as well as its own id. when updated, it will update	//
// its series as well as mysql. the series can then be used	//
// by the event class to define multiple events.		//
//////////////////////////////////////////////////////////////////
class module_strongcal_recurrance {

	//////////////////////////////////////////////////
	// $_id						//
	// the id of this recurrance pattern		//
	// found in the "recur_id" field of events	//
	//						//
	private $_id;					//
	//////////////////////////////////////////////////


	//////////////////////////////////////////////////
	// $_cal					//
	// the calendar to load this recurrance from	//
	//						//
	private $_cal;					//
	//////////////////////////////////////////////////


	//////////////////////////////////////////////////
	// $_start_time					//
	// the default start time for all events in	//
	//  the series.					//
	//						//
	private $_start_time;				//
	//////////////////////////////////////////////////


	//////////////////////////////////////////////////
	// $_end_time					//
	// the default end time for all events in	//
	//  the series.					//
	//						//
	private $_end_time;				//
	//////////////////////////////////////////////////


	//////////////////////////////////////////////////
	// $_start_date					//
	// the default start date for all events in	//
	//  the series.					//
	//						//
	private $_start_date;				//
	//////////////////////////////////////////////////


	//////////////////////////////////////////////////
	// $_end_type					//
	// defines how to end the recurrance		//
	//						//
	// possible values:				//
	//  RECUR_NO_END_DATE 				//
	//  RECUR_END_AFTER				//
	//  RECUR_END_BY				//
	//						//
	private $_end_type;				//
	//////////////////////////////////////////////////


	//////////////////////////////////////////////////
	// $_end_after					//
	// the number of occurances to end the series at//
	//						//
	private $_end_after;				//
	//////////////////////////////////////////////////


	//////////////////////////////////////////////////
	// $_end_date					//
	// the date to end the series by		//
	//						//
	private $_end_date;				//
	//////////////////////////////////////////////////


	//////////////////////////////////////////////////
	// $_recur_type					//
	// defines the main possible recurrance states	//
	//						//
	// possible values:				//
	//  RECUR_DAILY 				//
	//  RECUR_WEEKLY				//
	//  RECUR_MONTHLY				//
	//  RECUR_YEARLY				//
	//						//
	private $_recur_type;				//
	//////////////////////////////////////////////////


	//////////////////////////////////////////////////
	// application: a daily main recurrance		//
	//						//
	// $_day_count					//
	// the number of consecutive days to recur	//
	// "Every ___ day(s)"				//
	//						//
	private $_day_count;				//
	//////////////////////////////////////////////////


	//////////////////////////////////////////////////
	// application: a weekly main recurrance	//
	//						//
	// $_week_count					//
	// the increment of the number of weeks to	//
	// recur on					//
	//						//
	// "Recur every ___ week(s)			//
	private $_week_count;				//
	//////////////////////////////////////////////////


	//////////////////////////////////////////////////
	// application: a weekly main recurrance	//
	//						//
	// $_week_days					//
	// a string of numbers representing the weekdays//
	// the event should recur on			//
	//						//
	// Ex.						//
	//  "1367"					//
	//  will recur on Sunday, Tuesday, Friday,	//
	//  and Saturday				//
	private $_week_days;				//
	//////////////////////////////////////////////////


	//////////////////////////////////////////////////
	// application: a monthly main recurrance	//
	//						//
	// $_month_type					//
	// the monthly recurrance sub-type.		//
	//						//
	// possible values:				//
	//  RECUR_DAILY RECUR_WEEKLY			//
	private $_month_type;				//
	//////////////////////////////////////////////////


	//////////////////////////////////////////////////
	// application: a montly main recurrance	//
	//						//
	// $_month_day					//
	// in the case of RECUR_DAILY:			//
	//  the day of the month to recur on		//
	// in the case of RECUR_WEEKLY:			//
	//  the numerical day of the week to recur on	//
	//  Ex. "1" means Sunday			//
	//						//
	private $_month_day;				//
	//////////////////////////////////////////////////


	//////////////////////////////////////////////////
	// application: a montly main recurrance	//
	//						//
	// $_month_week					//
	// in the case of RECUR_DAILY:			//
	//  no data					//
	// in the case of RECUR_WEEKLY:			//
	//  the number of the week to recur on each	//
	//  month					//
	//						//
	private $_month_week;				//
	//////////////////////////////////////////////////


	//////////////////////////////////////////////////
	// application: a montly main recurrance	//
	//						//
	// $_month_weekday				//
	// in the case of RECUR_DAILY:			//
	//  no data					//
	// in the case of RECUR_WEEKLY:			//
	//  the number of the weekday to recur on for	//
	//  the appropriate week of the given month	//
	//						//
	private $_month_weekday;			//
	//////////////////////////////////////////////////


	//////////////////////////////////////////////////
	// application: a montly main recurrance	//
	//						//
	// $month_months				//
	// in the case of RECUR_DAILY:			//
	//  the increment of months to recur on		//
	//  "Every ____ months"				//
	// in the case of RECUR_WEEKLY:			//
	//  same as daily case				//
	//						//
	private $_month_months;				//
	//////////////////////////////////////////////////


	//////////////////////////////////////////////////
	// application: a yearly main recurrance	//
	//						//
	// $_year_type					//
	// the yearly recurrance sub-type.		//
	//						//
	// possible values:				//
	// RECUR_DAILY RECUR_WEEKLY			//
	//						//
	private $_year_type;				//
	//////////////////////////////////////////////////


	//////////////////////////////////////////////////
	// application: a yearly main recurrance	//
	//						//
	// $_year_month					//
	// in the case of RECUR_DAILY:			//
	//  the number of the month to recur on		//
	// in the case of RECUR_WEEKLY:			//
	//  the same as the daily case			//
	//						//
	private $_year_month;				//
	//////////////////////////////////////////////////


	//////////////////////////////////////////////////
	// application: a yearly main recurrance	//
	//						//
	// $_year_day					//
	// in the case of RECUR_DAILY			//
	//  the day of the month to recur on		//
	// in the case of RECUR_WEEKLY:			//
	//  no data					//
	//						//
	private $_year_day;				//
	//////////////////////////////////////////////////


	//////////////////////////////////////////////////
	// application: a yearly main recurrance	//
	//						//
	// $_year_week					//
	// in the case of RECUR_DAILY			//
	//  no data					//
	// in the case of RECUR_WEEKLY:			//
	//  the number of the week to recur on		//
	//						//
	private $_year_week;				//
	//////////////////////////////////////////////////


	//////////////////////////////////////////////////
	// application: a yearly main recurrance	//
	//						//
	// $_year_weekday				//
	// in the case of RECUR_DAILY			//
	//  no data					//
	// in the case of RECUR_WEEKLY:			//
	//  the day of the weekday to recur on		//
	//  Ex. 1 is Sunday				//
	//						//
	private $_year_weekday;				//
	//////////////////////////////////////////////////


	//////////////////////////////////////////////////
	// $_last_entry_date				//
	// the date of the last processed entry in the	//
	// series					//
	// 						//
	// [Note: specifically used in never ending	//
	//	  series]				//
	//						//
	private $_last_entry_date;			//
	//////////////////////////////////////////////////


	//////////////////////////////////////////////////
	// $_series					//
	// the list of valid start dates		//
	// 						//
	private $_series;				//
	//////////////////////////////////////////////////

//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
//   END VARIABLE DECLARATIONS   /////////////////////////
//                               /////////////////////////
//  BEGIN FUNCTION DECLARATIONS  /////////////////////////
//////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////


	//////////////////////////////////////////////////////////////////
	//  type()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the type of this recurrance pattern			//
	//		RECUR_DAILY					//
	//		RECUR_WEEKLY					//
	//		RECUR_MONTHLY					//
	//		RECUR_YEARLY					//  
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function type(){
		return $this->_recur_type;
	}

	//////////////////////////////////////////////////////////////////
	//  end_type()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the type of this recurrance pattern			//
	//		RECUR_NO_END_DATE				//
	//		RECUR_END_AFTER					//
	//		RECUR_END_BY					//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function endType(){
		return $this->_end_type;
	}
	
	//////////////////////////////////////////////////////////////////
	//  monthly_type()						//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the monthly type of this recurrance pattern		//
	//		RECUR_MONTHLY_DOM				//
	//		RECUR_MONTHLY_DOW				//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function monthlyType(){
		return $this->_month_type;
	}
	

	//////////////////////////////////////////////////////////////////
	//  yearly_type()						//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the yearly type of this recurrance pattern		//
	//		RECUR_YEARLY_DOM				//
	//		RECUR_YEARLY_DOW				//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function yearlyType(){
		return $this->_year_type;
	}
	


	//////////////////////////////////////////////////////////////////
	//  setEndType($end_type, $date_or_num=false)			//
	//--------------------------------------------------------------//
	//  input: $end_type - the new end type for this series		//
	//		must be one of following			//
	//		RECUR_NO_END_DATE				//
	//		RECUR_END_AFTER					//
	//		RECUR_END_BY					//
	//  output: none						//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function setEndType($end_type, $date_or_num=false){
		$this->_needs_load = true;
		if($this->_cal){
			$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" . $this->_cal->getId() . "_recur";
		}
		if($end_type == RECUR_NO_END_DATE){
			if($this->_cal){
				$sql = "UPDATE $tablename SET end_type='$end_type' WHERE id = '" . $this->getId() . "'";
				$this->avalanche->mysql_query($sql);
			}
			$this->_end_type = $end_type;
		}else
		if($end_type == RECUR_END_AFTER){
			if(is_numeric($date_or_num)){
				if($this->_cal){
					$sql = "UPDATE $tablename SET end_type='$end_type', end_after='$date_or_num' WHERE id = '" . $this->getId() . "'";
					$this->avalanche->mysql_query($sql);
				}
				$this->_end_type = $end_type;
				$this->_end_after = $date_or_num;
			}else{
				trigger_error("Second argument to setEndType(\$end_type, \$date_or_num) must be an integer when \$end_type equals RECUR_END_AFTER", E_USER_WARNING);
			}
		}else
		if($end_type == RECUR_END_BY){
			if(is_string($date_or_num)){
				if($this->_cal){
					$sql = "UPDATE $tablename SET end_type='$end_type', end_date='$date_or_num' WHERE id = '" . $this->getId() . "'";
					$this->avalanche->mysql_query($sql);
				}
				$this->_end_type = $end_type;
				$this->_end_date = $date_or_num;
			}else{
				trigger_error("Second argument to setEndType(\$end_type, \$date_or_num) must be a string of format \"yyyy-mm-dd\" when \$end_type equals RECUR_END_BY", E_USER_WARNING);
			}
		}else{
			trigger_error("Invalid input to function setEndType(\$end_type, \$date).", E_USER_WARNING);
		}
	}

	//////////////////////////////////////////////////////////////////
	//  setStartDate($start_date)					//
	//--------------------------------------------------------------//
	//  input: $start_date - the start date for this series		//
	//		must be one of the form:			//
	//		yyyy-mm-dd					//
	//  output: none						//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function setStartDate($start_date){
		$this->_needs_load = true;
		if($this->_cal){
			$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" . $this->_cal->getId() . "_recur";
		}
		if(is_string($start_date)){
			if($this->_cal){
				$sql = "UPDATE $tablename SET start_date='$start_date' WHERE id='" . $this->getId() . "'";
				$this->avalanche->mysql_query($sql);
			}
			$this->_start_date = $start_date;
		}else{
			trigger_error("Invalid input to function setStartDate(\$start_date). \$start_date must be a string.", E_USER_WARNING);
		}
	}


	//////////////////////////////////////////////////////////////////
	//  setToDaily($day_count)					//
	//--------------------------------------------------------------//
	//  input: $day_count - the number of days to recur		//
	//		"Recur every ____ day(s)"			//
	//  output: none						//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function setToDaily($day_count){
		$this->_needs_load = true;
		if($this->_cal){
			$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" . $this->_cal->getId() . "_recur";
		}
		if(is_numeric($day_count)){
			if($this->_cal){
				$sql = "UPDATE $tablename SET recur_type='" . RECUR_DAILY . "', day_count='$day_count', week_count='0', week_days='', month_type='0', month_day='0', month_week='0', month_weekday='', month_months='0', year_type='0', year_day='0', year_week='0', year_weekday='', year_m='0'  WHERE id = '" . $this->getId() . "'";
				$this->avalanche->mysql_query($sql);
			}
			$this->_recur_type = RECUR_DAILY;
			$this->_day_count = $day_count;
		}else{
			trigger_error("Argument \$day_count in setToDaily(\$day_count) must be an integer", E_USER_WARNING);
		}
	}


	//////////////////////////////////////////////////////////////////
	//  setToWeekly($week_count, $week_days)			//
	//--------------------------------------------------------------//
	//  input: $week_count - the number of weeks to recur		//
	//		"Recur every ____ week(s)"			//
	//	   $week_days - a string representatio of what weekdays //
	//		to recur on. Each day is represented by its	//
	//		number.						//
	//		0 (for Sunday) through 6 (for Saturday)		// 
	//  output: none						//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function setToWeekly($week_count, $week_days){
		$this->_needs_load = true;
		if($this->_cal){
			$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" . $this->_cal->getId() . "_recur";
		}
		if(is_numeric($week_count) && is_string($week_days)){
			if($this->_cal){
				$sql = "UPDATE $tablename SET recur_type='" . RECUR_WEEKLY . "', day_count='0', week_count='$week_count', week_days='$week_days', month_type='0', month_day='0', month_week='0', month_weekday='', month_months='0', year_type='0', year_day='0', year_week='0', year_weekday='', year_m='0'  WHERE id = '" . $this->getId() . "'";
				$this->avalanche->mysql_query($sql);
			}
			$this->_recur_type = RECUR_WEEKLY;
			$this->_week_count = $week_count;
			$this->_week_days = $week_days;
		}else{
			if(!is_numeric($week_count)){
				trigger_error("Argument \$week_count in setToWeeky(\$week_count, \$week_days) must be an integer", E_USER_WARNING);
			}
			if(!is_string($week_days)){
				trigger_error("Argument \$week_days in setToWeeky(\$week_count, \$week_days) must be a string", E_USER_WARNING);
			}
		}
	}


	//////////////////////////////////////////////////////////////////
	//  setToMonthly($month_type, $month_day_or_week, 		//
	//		 $month_weekday, $month_months)			//
	//--------------------------------------------------------------//
	//  input: $month_type - the type of monthly recurrance		//
	//		either:						//
	//		  RECUR_MONTHLY_DOM				//
	//		  RECUR_MONTHLY_DOW				//
	//	   $month_day_or_week - the day of the month to recur	//
	//		on in the case of DOM, or the number of the	//
	//		week in the case of DOW.			//
	//	   $month_weekday - the integer representation of the	//
	//		weekday to recur on (only applies to DOW)	//
	//		0 (for Sunday) through 6 (for Saturday)		// 
	//  output: none						//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function setToMonthly($month_type, $month_months, $month_day_or_week, $month_weekday=false){
		$this->_needs_load = true;
		if($this->_cal){
			$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" . $this->_cal->getId() . "_recur";
		}
		if(is_numeric($month_months) && is_numeric($month_day_or_week) && $month_type == RECUR_MONTHLY_DOM){
			if($this->_cal){
				$sql = "UPDATE $tablename SET recur_type='" . RECUR_MONTHLY . "', day_count='0', week_count='0', week_days='0', month_type='$month_type', month_day='$month_day_or_week', month_week='0', month_weekday='', month_months='$month_months', year_type='0', year_day='0', year_week='0', year_weekday='', year_m='0'  WHERE id = '" . $this->getId() . "'";
				$this->avalanche->mysql_query($sql);
			}
			$this->_recur_type = RECUR_MONTHLY;
			$this->_month_type = $month_type;
			$this->_month_months = $month_months;
			$this->_month_day  = $month_day_or_week;
			$this->_month_week = $month_day_or_week;
			$this->_month_weekday = $month_weekday;
		}else
		if(is_numeric($month_months) && is_numeric($month_day_or_week) && is_numeric($month_weekday)&& $month_type == RECUR_MONTHLY_DOW){
			if($this->_cal){
				$sql = "UPDATE $tablename SET recur_type='" . RECUR_MONTHLY . "', day_count='0', week_count='0', week_days='0', month_type='$month_type', month_day='0', month_week='$month_day_or_week', month_weekday='$month_weekday', month_months='$month_months', year_type='0', year_day='0', year_week='0', year_weekday='', year_m='0'  WHERE id = '" . $this->getId() . "'";
				$this->avalanche->mysql_query($sql);
			}
			$this->_recur_type = RECUR_MONTHLY;
			$this->_month_type = $month_type;
			$this->_month_months = $month_months;
			$this->_month_day  = $month_day_or_week;
			$this->_month_week = $month_day_or_week;
			$this->_month_weekday = $month_weekday;
		}else{
			if(!is_numeric($month_type)){
				trigger_error("Argument \$month_type in setToMonthly(\$month_type, \$month_months, \$month_day_or_week, \$month_weekday=false) must be an integer", E_USER_WARNING);
			}
			if(!is_numeric($month_months)){
				trigger_error("Argument \$month_months in setToMonthly(\$month_type, \$month_months, \$month_day_or_week, \$month_weekday=false) must be an integer", E_USER_WARNING);
			}
			if(!is_numeric($month_day_or_week)){
				trigger_error("Argument \$month_day_or_week in setToMonthly(\$month_type, \$month_months, \$month_day_or_week, \$month_weekday=false) must be an integer", E_USER_WARNING);
			}
			if(!is_numeric($month_weekday) && $month_type == RECUR_MONTHLY_DOW){
				trigger_error("Argument \$month_weekday in setToMonthly(\$month_type, \$month_months, \$month_day_or_week, \$month_weekday=false) must be an integer", E_USER_WARNING);
			}
		}
	}



	//////////////////////////////////////////////////////////////////
	//  setToYearly($year_type, $year_day_or_week, 			//
	//		 $year_weekday, $year_month)			//
	//--------------------------------------------------------------//
	//  input: $month_type - the type of monthly recurrance		//
	//		either:						//
	//		  RECUR_YEARLY_DOM				//
	//		  RECUR_YEARLY_DOW				//
	//	   $month_day_or_week - the day of the month to recur	//
	//		on in the case of DOM, or the number of the	//
	//		week in the case of DOW.			//
	//	   $month_weekday - the integer representation of the	//
	//		weekday to recur on (only applies to DOW)	//
	//		0 (for Sunday) through 6 (for Saturday)		// 
	//  output: none						//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function setToYearly($year_type, $year_day_or_week, $year_month, $year_weekday = false){
		$this->_needs_load = true;
		if(is_object($this->_cal)){
			$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" . $this->_cal->getId() . "_recur";
		}
		if(is_numeric($year_month) && is_numeric($year_day_or_week) && ($year_type == RECUR_YEARLY_DOM)){
			if(is_object($this->_cal)){
				$sql = "UPDATE $tablename SET recur_type='" . RECUR_YEARLY . "', day_count='0', week_count='0', week_days='0', month_type='0', month_day='0', month_week='0', month_weekday='', month_months='0', year_type='$year_type', year_day='$year_day_or_week', year_week='0', year_weekday='', year_m='$year_month'  WHERE id = '" . $this->getId() . "'";
				$this->avalanche->mysql_query($sql);
			}
			$this->_recur_type = RECUR_YEARLY;
			$this->_year_type  = $year_type;
			$this->_year_month = $year_month;
			$this->_year_day  = $year_day_or_week;
			$this->_year_week = $year_day_or_week;
			$this->_year_weekday = $year_weekday;
		}else
		if(is_numeric($year_month) && is_numeric($year_day_or_week) && is_numeric($year_weekday) && ($year_type == RECUR_YEARLY_DOW)){
			if(is_object($this->_cal)){
				$sql = "UPDATE $tablename SET recur_type='" . RECUR_YEARLY . "', day_count='0', week_count='0', week_days='0', month_type='0', month_day='0', month_week='0', month_weekday='', month_months='0', year_type='$year_type', year_day='0', year_week='$year_day_or_week', year_weekday='$year_weekday', year_m='$year_month'  WHERE id = '" . $this->getId() . "'";
				$this->avalanche->mysql_query($sql);
			}
			$this->_recur_type = RECUR_YEARLY;
			$this->_year_type = $year_type;
			$this->_year_month = $year_month;
			$this->_year_day  = $year_day_or_week;
			$this->_year_week = $year_day_or_week;
			$this->_year_weekday = $year_weekday;
		}else{
			if(!is_numeric($year_type)){
				trigger_error("Argument \$year_type in setToYearly(\$year_type, \$year_day_or_week, \$year_month, \$year_weekday = false) must be an integer", E_USER_WARNING);
			}
			if(!is_numeric($year_month)){
				trigger_error("Argument \$year_month in setToYearly(\$year_type, \$year_day_or_week, \$year_month, \$year_weekday = false) must be an integer", E_USER_WARNING);
			}
			if(!is_numeric($year_day_or_week)){
				trigger_error("Argument \$year_day_or_week in setToYearly(\$year_type, \$year_day_or_week, \$year_month, \$year_weekday = false) must be an integer", E_USER_WARNING);
			}
			if(!is_numeric($year_weekday) && $year_type == RECUR_MONTHLY_DOW){
				trigger_error("Argument \$year_weekday in setToYearly(\$year_type, \$year_day_or_week, \$year_month, \$year_weekday = false) must be an integer", E_USER_WARNING);
			}
		}
	}

/////////////////////////////////
/////////////////////////////////
/////////////////////////////////
/////  still have yearly  ///////
/////        to do        ///////
/////////////////////////////////
/////////////////////////////////
/////////////////////////////////




	//////////////////////////////////////////////////////////////////
	//  getId()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the id of this recurrance				//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function getId(){
		return $this->_id;
	}

	//////////////////////////////////////////////////////////////////
	//  calendar()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: the calendar of this recurrance			//
	//								//  
	//  precondition:						//  
	//	object must be initialized				//
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function calendar(){
		return $this->_cal;
	}

	//////////////////////////////////////////////////////////////////
	//  getProperty($prop)						//
	//--------------------------------------------------------------//
	//  input: a string of a property name				//
	//		allowed values:					//
	//			start_time				//
	//			end_time				//
	//			start_date				//
	//			end_type				//
	//			end_after				//
	//			end_date				//
	//			recur_type				//
	//			day_count				//
	//			week_count				//
	//			week_days				//
	//			month_type				//
	//			month_day				//
	//			month_week				//
	//			month_weekday				//
	//			month_months				//
	//			year_type				//
	//			year_month				//
	//			year_day				//
	//			year_week				//
	//			year_weekday				//
	//  output: the value of that property				//
	//////////////////////////////////////////////////////////////////
	function getProperty($prop){
		$prop = "_$prop";
		return $this->$prop;
	}




	//////////////////////////////////////////////////////////////////
	//  compareTo($recurance)					//
	//--------------------------------------------------------------//
	//  input: $recurrance - the recurrance patter to compare to	//
	//								//  
	//  output: boolean, true if fields are equal			//
	//								//  
	//  precondition:						//  
	//								//
	//  postcondition:						//
	//								//
	//////////////////////////////////////////////////////////////////
	function compareTo($recurrance, $strict = true){
		if($this->_start_time == $recurrance->_start_time  &&
		   $this->_end_time   == $recurrance->_end_time    &&
		   $this->_start_date == $recurrance->_start_date  &&
		   $this->_end_type   == $recurrance->_end_type    &&
		   $this->_end_after  == $recurrance->_end_after   &&
		   $this->_recur_type == $recurrance->_recur_type  &&
		   $this->_day_count  == $recurrance->_day_count   &&
		   $this->_week_count == $recurrance->_week_count  &&
		   $this->_month_type == $recurrance->_month_type  &&
		   $this->_month_day  == $recurrance->_month_day   &&
		   $this->_month_week == $recurrance->_month_week  &&
		   $this->_month_weekday == $recurrance->_month_weekday &&
		   $this->_month_months  == $recurrance->_month_months  &&
		   $this->_year_type     == $recurrance->_year_type     &&
		   $this->_year_month    == $recurrance->_year_month    &&
		   $this->_year_day      == $recurrance->_year_day      &&
		   $this->_year_week     == $recurrance->_year_week     &&
		   $this->_year_weekday  == $recurrance->_year_weekday  &&
		   (!$strict || $strict &&
		   $this->_last_entry_date == $recurrance->last_entry_date &&
		   $this->getId() == $recurrance->getId())
		  ){
			return true;
		}else{
			return false;
		}
	}


	//////////////////////////////////////////////////////////////////
	//  init($cal, $id)						//
	//--------------------------------------------------------------//
	//  input: $cal - the calendar object to which this series	//
	//		  belongs					//
	//  output: the id of this series				//
	//								//
	//  precondition:						//
	//	$id must be a vaid recurrance id			//
	//  postcondition:						//
	//	all fields are initialized to apprpriate values		//
	//////////////////////////////////////////////////////////////////
	private $avalanche;
	function __construct($avalanche, $cal = false, $id=false){
		$this->avalanche = $avalanche;
		$this->_needs_load = true;
		$this->_cal = $cal;
		$this->_id = $id;

		if(is_object($cal) && $cal->is_serialized()){
			return false;
		}


		if(is_object($cal) && $id){
			$sql = "SELECT * FROM " . $this->avalanche->PREFIX() . "strongcal_cal_" . $cal->getId() . "_recur WHERE id = '$id'";
			$result = $this->avalanche->mysql_query($sql);
		}
		while(is_object($cal) && $id && $myrow = mysql_fetch_array($result)){
			$this->_id            = $myrow['id'];
			$this->_start_time    = $myrow['start_time'];
			$this->_end_time      = $myrow['end_time'];
			$this->_start_date    = $myrow['start_date'];
			$this->_end_type      = $myrow['end_type'];
			$this->_end_after     = $myrow['end_after'];
			$this->_end_date      = $myrow['end_date'];
			$this->_recur_type    = $myrow['recur_type'];
			$this->_day_count     = $myrow['day_count'];
			$this->_week_count    = $myrow['week_count'];
			$this->_week_days     = $myrow['week_days'];
			$this->_month_type    = $myrow['month_type'];
			$this->_month_day     = $myrow['month_day'];
			$this->_month_week    = $myrow['month_week'];
			$this->_month_weekday = $myrow['month_weekday'];
			$this->_month_months  = $myrow['month_months'];
			$this->_year_type     = $myrow['year_type'];
			$this->_year_month    = $myrow['year_m'];
			$this->_year_day      = $myrow['year_day'];
			$this->_year_week     = $myrow['year_week'];
			$this->_year_weekday  = $myrow['year_weekday'];
			$this->_last_entry_date    = $myrow['last_entry_date'];
		}
		if(!is_object($cal) || !$id){
			$this->_id            = false;
			$this->_start_time    = false;
			$this->_end_time      = false;
			$this->_start_date    = false;
			$this->_end_type      = false;
			$this->_end_after     = false;
			$this->_end_date      = false;
			$this->_recur_type    = false;
			$this->_day_count     = false;
			$this->_week_count    = false;
			$this->_week_days     = false;
			$this->_month_type    = false;
			$this->_month_day     = false;
			$this->_month_week    = false;
			$this->_month_weekday = false;
			$this->_month_months  = false;
			$this->_year_type     = false;
			$this->_year_month    = false;
			$this->_year_day      = false;
			$this->_year_week     = false;
			$this->_year_weekday  = false;
			$this->_last_entry_date    = false;
		}



	}




	//////////////////////////////////////////////////////////////////
	//  initFor($cal)						//
	//  creates a new recurrance object in the calendar		//
	//--------------------------------------------------------------//
	//  input: $cal - the calendar object to which this series	//
	//		  belongs					//
	//  output: $this						//
	//								//
	//  precondition:						//
	//	$id must be either false to load a blank recurrance	//
	//	object, or a vaid recurrance id				//
	//  postcondition:						//
	//	all fields are initialized to apprpriate values		//
	//////////////////////////////////////////////////////////////////
	function initFor($cal){
		$this->_needs_load = true;
		$this->_cal = $cal;
		$tablename = $this->avalanche->PREFIX() . "strongcal_cal_" . $cal->getId() . "_recur";

		$sql = "INSERT INTO $tablename (`start_time`) VALUES ('00:00:00')";
		$result = $this->avalanche->mysql_query($sql);

		$id = mysql_insert_id();
		$this->_id = $id;
		return $this;
	}


	//////////////////////////////////////////////////////////////////
	//  update()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: boolean - true if updated successfully		//
	//		      false otherwise				//
	//								//
	//  precondition:						//
	//	object must be in a valid recurrance state		//
	//  postcondition:						//
	//	series of valid dates is recalculated			//
	//////////////////////////////////////////////////////////////////
	function update(){
		if($this->_recur_type && $this->_needs_load){
			$this->_needs_load = false;
			$ret = array();
			$start_date = $this->_start_date;
			$end_date = $this->_end_date;
			if($this->_end_type == RECUR_NO_END_DATE){
				$end_date = RECUR_MAX_DATE;
			}

			$start_date = mktime ( 0, 0, 0, substr($start_date, 5,2), substr($start_date, 8,2), substr($start_date, 0,4));
			$end_date  = mktime ( 0, 0, 0, substr($end_date, 5,2), substr($end_date, 8,2), substr($end_date, 0,4));

			if($end_date < $start_date && $this->_end_type == RECUR_END_BY){
				// functionality here is undefined.
				trigger_error("Event Error: End date is set to earlier than start date. Recur Id: " . $this->getId(), E_USER_WARNING);
			}


			//$start_date and $end_date are unix timestamps
			if($this->_recur_type == RECUR_DAILY){
				$events_count=0;
				while(($this->_day_count > 0) && $this->_end_type != RECUR_END_AFTER &&
					((date("Y-m-d", $start_date) <= date("Y-m-d", $end_date))) ||
					($this->_end_type == RECUR_END_AFTER && ($events_count < $this->_end_after ))){
					$events_count++;
					$ret[] = date("Y-m-d", $start_date);
					$start_date = strtotime ("+" . $this->_day_count . " days", $start_date);
				}
			}else
			if($this->_recur_type == RECUR_WEEKLY){
				$events_count=0;
				$count=0;
				$week_count=0;
				while(($this->_end_type != RECUR_END_AFTER && date("Y-m-d", $start_date) <= date("Y-m-d", $end_date)) ||
					($this->_end_type == RECUR_END_AFTER && $events_count < $this->_end_after)){
					$count++;
					if($count % 7 == 0){
						$week_count++;
					}
					if((strpos($this->_week_days, date("w", $start_date)) !== false) && ($week_count % $this->_week_count == 0)){
						$events_count++;
						$ret[] = date("Y-m-d", $start_date);
					}
					$start_date = strtotime ("+1 day", $start_date);
				}
			}else
			if($this->_recur_type == RECUR_MONTHLY){
				if($this->_month_type == RECUR_MONTHLY_DOM){
					$start_month = date("m", $start_date);
					$events_count=0;
					$month_count = 0;
					while($this->_end_type != RECUR_END_AFTER && (date("Y-m-d", $start_date) <= date("Y-m-d", $end_date)) ||
						($this->_end_type == RECUR_END_AFTER && $events_count < $this->_end_after)){
						if($start_month != date("m", $start_date)){
							$month_count++;
							$start_month = date("m", $start_date);
						}
						if($this->_month_day == date("d", $start_date) && 
						   ($month_count % $this->_month_months == 0) &&
						   date("Y-m-d", $start_date) >= $this->_start_date){
							$events_count++;
							$ret[] = date("Y-m-d", $start_date);


							/*
							 * we know the next event won't be in the next 27 (26 + 1) days, so lets skip 'em
							 */
							$start_date = strtotime ("+26 day", $start_date);
						}
						$start_date = strtotime ("+1 day", $start_date);
					}
				}else
				if($this->_month_type == RECUR_MONTHLY_DOW){
					$start_date = mktime ( 0, 0, 0, substr(date("Y-m-d", $start_date), 5,2), 1, substr(date("Y-m-d", $start_date), 0,4));
					$start_month = date("m", $start_date);
					$events_count=0;
					$dow_count=0;
					$month_count = 0;
					while($this->_end_type != RECUR_END_AFTER && (date("Y-m-d", $start_date) <= date("Y-m-d", $end_date)) ||
						($this->_end_type == RECUR_END_AFTER && $events_count < $this->_end_after)){
						if($start_month != date("m", $start_date)){
							$dow_count = 0;
							$month_count++;
							$start_month = date("m", $start_date);
						}
						if($this->_month_weekday == date("w", $start_date)){
							$dow_count++;
						}
						if(($this->_month_week == $dow_count) && 
							($this->_month_weekday == date("w", $start_date)) && 
							($month_count % $this->_month_months == 0) &&
							date("Y-m-d", $start_date) >= $this->_start_date){
							$events_count++;
							$ret[] = date("Y-m-d", $start_date);
							/*
							 * we know the next event won't be in the next 21 (20 + 1) days, so lets skip 'em
							 */
							$start_date = strtotime ("+20 day", $start_date);
						}
					$start_date = strtotime ("+1 day", $start_date);
					}
				}
			}else
			if($this->_recur_type == RECUR_YEARLY){
				$start_date = mktime ( 0, 0, 0, substr(date("Y-m-d", $start_date), 5,2), 0, substr(date("Y-m-d", $start_date), 0,4));
				if($this->_year_type == RECUR_YEARLY_DOM){
					$start_month = date("m", $start_date);
					$events_count=0;
					while($this->_end_type != RECUR_END_AFTER && (date("Y-m-d", $start_date) <= date("Y-m-d", $end_date)) ||
						($this->_end_type == RECUR_END_AFTER && $events_count < $this->_end_after )){
						if($this->_year_day == date("d", $start_date) && 
							($this->_year_month == date("m", $start_date))){
							$events_count++;
							$ret[] = date("Y-m-d", $start_date);
							/*
							 * we know the next event won't be in the next 11 months and 21 days (20 + 1) days, so lets skip 'em
							 */
							$start_date = strtotime ("+11 months", $start_date);
							$start_date = strtotime ("+20 days", $start_date);
						}
					$start_date = strtotime ("+1 day", $start_date);
					}
				}else
				if($this->_year_type == RECUR_YEARLY_DOW){
					$start_year  = date("Y", $start_date);
					$start_month = date("m", $start_date);
					$dow_count=0;
					$events_count=0;
					while($this->_end_type != RECUR_END_AFTER && (date("Y-m-d", $start_date) <= date("Y-m-d", $end_date)) ||
						($this->_end_type == RECUR_END_AFTER && $events_count < $this->_end_after)){
						if($start_month != date("m", $start_date) ||
						   $start_year  != date("Y", $start_date)){
							$dow_count = 0;
							$start_year  = date("Y", $start_date);
							$start_month = date("m", $start_date);
						}
						if($this->_year_weekday == date("w", $start_date)){
							$dow_count++;
						}
						if(($this->_year_week == $dow_count) && 
							($this->_year_weekday == date("w", $start_date)) && 
							($this->_year_month == date("m", $start_date))){
							$events_count++;
							$ret[] = date("Y-m-d", $start_date);
							/*
							 * we know the next event won't be in the next 11 months and 21 days (20 + 1) days, so lets skip 'em
							 */
							$dow_count = 0;
							$start_date = strtotime ("+11 months", $start_date);
							$start_date = strtotime ("+20 days", $start_date);
							$start_date = mktime ( 0, 0, 0, substr(date("Y-m-d", $start_date), 5,2), 0, substr(date("Y-m-d", $start_date), 0,4));
						}
					$start_date = strtotime ("+1 day", $start_date);
					}
				}
			}
			$this->_series = $ret;
		}else{
		}
	}


	//////////////////////////////////////////////////////////////////
	//  series()							//
	//--------------------------------------------------------------//
	//  input: none							//
	//  output: an array of valid dates				//
	//								//
	//  precondition:						//
	//	object must have been updated()'d			//
	//	object must be in a valid recurrance state		//
	//  postcondition:						//
	//	series of valid dates is recalculated			//
	//////////////////////////////////////////////////////////////////
	function series(){
		if($this->_needs_load){
			$this->update();
		}
		return $this->_series;
	}

}

?>