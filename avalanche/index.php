<?
/**
 * automoatically loads install.php
 *
 */


$self = $_SERVER['PHP_SELF'];
$root = $_SERVER['HTTP_HOST'];

$pos_of_last_slash = strrpos($self, "//");

$pos_of_last_slash++;


/**
 * move to character one to the right of the last slash in the filename
 */
$self = substr_replace($self, "installerui.php", $pos_of_last_slash);


header("Location: http://" . $root . $self);

?>