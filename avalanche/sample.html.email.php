<?

try{
	include "../include.avalanche.fullApp.php";

	$to = "awulf@inversiondesigns.com";
	$headers = "From: adam.wulf@gmail.com\n";
	$subject = "buddy buddy sample";
	$body = "<html><head><title>buddy buddy sample title in html</title></head><body>";
	$body .= "<b>this</b> is a <a href='http://inversiondesigns.com'>link</a>.<br><br>you bet!<br>";
	$body .= "</body></html>";
	$avalanche->HTMLmail($to, $subject, $body, $headers);
	
}catch(Exception $e){
	if(is_object($avalanche)){
		$body = "Account Communication Cron Failure!!!\n<br>";
	}else{
		$body = "Could not load Avalanche!";
	}
	$body .= "Cron Failure!!!<br>\n<br>\n";
	$body .= $e->getFile() . "<br>\n<br>\n";
	$body .= $e->getMessage() . "<br>\n<br>\n";
	$body .= str_replace("\n", "<br>\n", $e->getTraceAsString());
	mail("awulf@inversiondesigns.com", "Bootstrap Exception", $body); 
}
echo $body;
?>