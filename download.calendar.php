<?

try{
	include "include.avalanche.fullApp.php";
	$strongcal = $avalanche->getModule("strongcal");
	$stamp = $strongcal->gmttimestamp();
	$date = date("Y-m-d", $stamp);
	$body = $strongcal->execute(new ExportCalendarVisitor($avalanche, $date));
	
}catch(Exception $e){
	if(is_object($avalanche)){
		$body = "Calendar Export Failure!!!\n<br>";
	}else{
		$body = "Could not load Avalanche!";
	}
	$body .= $e->getFile() . "<br>\n<br>\n";
	$body .= $e->getMessage() . "<br>\n<br>\n";
	$body .= str_replace("\n", "<br>\n", $e->getTraceAsString());
	mail("awulf@inversiondesigns.com", "Bootstrap Exception", $body); 
}


header("Content-Type: text/x-iCalendar");
header("Content-Disposition: inline; filename=schedule.ics");
echo $body;
?>