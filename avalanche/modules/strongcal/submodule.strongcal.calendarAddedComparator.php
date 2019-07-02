<?
//be sure to leave no white space before and after the php portion of this file
//headers must remain open after this file is included


//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
///////////////                         //////////////////////////
///////////////  STRONGCAL SUB-MODULE   //////////////////////////
///////////////    eventComparator      //////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
// this field class defines a field that knows its home calendar//
// and own id. when updated, it will update mysql.		//
//////////////////////////////////////////////////////////////////
// THIS CLASS NEEDS UPDATING SO THAT MYSQL WILL UPDATE		//
// APPROPRIATELY WHEN THE FIELD IS RESET.			//
//////////////////////////////////////////////////////////////////
class StrongcalCalendarAddedComparator implements Comparator{

   public function __construct(){
   }

   ///////////////////////////////////////////////////////
   // returns < 0 if the left is less than the right	//
   // returns > 0 if the left is greater than the right //
   // returns 0 if the left is the same as the right	//
   ///////////////////////////////////////////////////////
   public function compare($left, $right){
	if(!(is_object($left) && ($left instanceof module_strongcal_calendar))){
		throw new IllegalArgumentException("The first argument must be of type module_strongcal_calendar");
	}
	if(!(is_object($right) && ($right instanceof module_strongcal_calendar))){
		throw new IllegalArgumentException("The second argument  must be of type module_strongcal_calendar");
	}

	if($left->addedOn() < $right->addedOn()){
		return -1;
	}else
	if($left->addedOn() > $right->addedOn()){
		return 1;
	}else
	if($left->getId() < $right->getId()){
		return -1;
	}else
	if($left->getId() > $right->getId()){
		return 1;
	}else{
		return 0;
	}
   }


}