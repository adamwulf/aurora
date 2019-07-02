<?php

$account = $_REQUEST["account"];
$user_id = $_REQUEST["user_id"];

include "../../$account/include.avalanche.fullApp.php";


$user = $avalanche->getUser($user_id);
echo $user->avatar();

?>