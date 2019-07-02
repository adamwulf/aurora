<?php

$account = $_REQUEST["account"];
$user_id = $_REQUEST["user_id"];

include "../../$account/include.avalanche.fullApp.php";


$filename = $avalanche->HOSTURL() . $avalanche->getAvatar($user_id);
list($width, $height, $type, $attr) = getimagesize($filename);
$type = image_type_to_mime_type($type);
header("Content-type: $type");
echo $avalanche->getAvatarContents($user_id);

?>