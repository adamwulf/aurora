<?

try{
	include "../include.avalanche.fullApp.php";
	$accounts = $avalanche->getModule("accounts");
	
	$body = $avalanche->execute(new AccountsCommunicationVisitor($avalanche));
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