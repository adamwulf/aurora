<?


echo "this part of the script runs fine.";
echo "if i exit here, then alls well.";


exit;

$mysql = mysql_connect("localhost", "invers", "samplepassword");

echo " but down here nothing happens.";
echo " the mysql_connect destroys php ?!";

exit;


?>