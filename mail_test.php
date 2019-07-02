<?



include "include.avalanche.fullApp.php";

$to = array("awulf@rice.edu");

for($i=0;$i<count($to);$i++){
	echo "mailing to: " . $to[$i] . "<br>";
	echo "result: " . $avalanche->mail($to[$i], "test to " . $to[$i], "using avalanche->mail. this is a test", "From: Adam Wulf <adam.wulf@gmail.com>\r\nReply-To: adam.wulf@gmail.com") . "<br>";
	echo "done.<br>";
}










?>

