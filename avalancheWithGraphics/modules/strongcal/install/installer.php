<?
$correction_dir = "../../";
include "../../include.module.install.php";

$global = $avalanche;

$skin = $global->getSkin("installer");
$buffer_skin = $global->getSkin("buffer");
$extra = "onmouseover=\"setPointer(this, '". $skin->thcolor() ."')\" onmouseout=\"setPointer(this, '". $skin->tdcolor() ."')\"";
$hidden_type = "hidden";

if(!$step_num){
	$step_num=1;
}


if ($step_num == 1) {
	
	$nextText = $skin->font(". next .");
	$next_button = $skin->a($nextText, "href='installer.php?step_num=2'");
	
	$headText = $skin->font(". welcome . to . phpAvalanche .");
	$top_row = $skin->td($headText, "valign='bottom' align='left' height='100' width='300'");
	$top_row = $skin->tr($top_row);
	$mid_row = $skin->td("<img src='../images/phpavalanche.jpg' height='150' width='300'>", " height='150' width='300'");
	$mid_row = $skin->tr($mid_row);
	$bot_row = $skin->td($next_button, "align='right' height='100' width='300'");
	$bot_row = $skin->tr($bot_row);
	
	$everything = $skin->table($top_row . $mid_row . $bot_row, "cellpadding='0' cellspacing='0' height='390'");

}


if ($step_num == 2) {
	
	$head = $skin->p_title(". terms of service .");
	$tos_frame = "<iframe src='tos.php' frameborder='0' width='300' height='310' name='tosframe'>browser must support iframes for installation to occur</iframe>";
	
	
	$acceptText = $skin->font(". accept .");
	$declineText = $skin->font(". decline .");
	$accept_button = $skin->a($acceptText, "href='installer.php?step_num=3'");
	$decline_button = $skin->a($declineText, "href='installer.php'");
	
	$accept_button = $buffer_skin->td($accept_button, "width='100%' align='center'");
	$accept_button = $buffer_skin->tr($accept_button, $extra);
	$accept_button = $buffer_skin->table($accept_button, "cellpadding='0' cellspacing='0' width='100%' height='100%'");
	
	$decline_button = $buffer_skin->td($decline_button, "width='100%' align='center'");
	$decline_button = $buffer_skin->tr($decline_button, $extra);
	$decline_button = $buffer_skin->table($decline_button, "cellpadding='0' cellspacing='0' width='100%' height='100%'");
	
	$accept_button = $skin->td($accept_button, "width='100' height='25' align='center'");
	$decline_button = $skin->td($decline_button, "width='100' height='25' align='center'");
	$mid_buff = $skin->td("", "width='100' height='25'");
	
	$top_row = $skin->th($head, "colspan=3'");
	$top_row = $skin->tr($top_row);
	$mid_row = $skin->td($tos_frame, "width='300' colspan=3");
	$mid_row = $skin->tr($mid_row);
	$bot_row = $skin->tr($decline_button . $mid_buff . $accept_button);
	
	$everything = $skin->table($top_row . $mid_row . $bot_row, "cellpadding='0' cellspacing='0' height='390'");
		
}


if ($step_num == 3) {
	$form_start = "<form action='installer.php' method='post' name='form'>";
	$form_hidden = "<input type='$hidden_type' name='step_num' value='4'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_adminname' value='$sql_adminname'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_passname' value='$sql_passname'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_dbname' value='$sql_dbname'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_hostname' value='$sql_hostname'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_prefix' value='$sql_prefix'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_overwrite' value='$sql_overwrite'>";
	$form_hidden .= "<input type='$hidden_type' name='username' value='$username'>";
	$form_hidden .= "<input type='$hidden_type' name='password' value='$password'>";
	$form_hidden .= "<input type='$hidden_type' name='passwordv' value='$passwordv'>";
	$form_hidden .= "<input type='$hidden_type' name='root_directory' value='$root_directory'>";
	$form_hidden .= "<input type='$hidden_type' name='at_path' value='$at_path'>";
	$form_hidden .= "<input type='$hidden_type' name='host' value='$host'>";
	$form_hidden .= "<input type='$hidden_type' name='domain' value='$domain'>";
	$form_hidden .= "<input type='$hidden_type' name='cookies' value='$cookies'>";
	$form_end = "</form>";	

	include 'checking.php';
	$head = $skin->p_title(". checking files .");
	$check_frame = "<iframe src='checking.php?show=1' frameborder='0' width='300' height='310' name='checkframe'>browser must support iframes for installation to occur</iframe>";
	
	$mid_frame = $skin->td($check_frame, "width='300' colspan='3'");
	
	
	$nextText = $skin->font(". next .");
	$backText = $skin->font(". back .");
	$next_button = $skin->a($nextText, "href='installer.php?step_num=4'");
	$back_button = $skin->a($backText, "href='installer.php?step_num=2&sql_overwrite=$sql_overwrite&sql_prefix=$sql_prefix&sql_adminname=$sql_adminname&sql_passname=$sql_passname&sql_dbname=$sql_dbname&sql_hostname=$sql_hostname&username=$username&password=$password&passwordv=$passwordv&root_directory=$root_directory&at_path=$at_path&host=$host&domain=$domain&cookies=$cookies'");



	$next_button = $buffer_skin->td($next_button, "width='100%' align='center'");
	if(!filesAreOk()){
		$next_button = "";
		$next_button = $buffer_skin->tr($next_button, "");
	}else{
		$next_button = $buffer_skin->tr($next_button, $extra);
	}
	$next_button = $buffer_skin->table($next_button, "cellpadding='0' cellspacing='0' width='100%' height='100%'");
	
	$back_button = $buffer_skin->td($back_button, "width='100%' align='center'");
	$back_button = $buffer_skin->tr($back_button, $extra);
	$back_button = $buffer_skin->table($back_button, "cellpadding='0' cellspacing='0' width='100%' height='100%'");
	
	$next_button = $skin->td($next_button, "width='100' height='25' align='center'");
	$back_button = $skin->td($back_button, "width='100' height='25' align='center'");
	$mid_buff = $skin->td("", "width='100' height='25'");
		
	$top_row = $skin->th($head, "colspan='3'");
	$top_row = $skin->tr($top_row);
	$mid_row = $skin->tr($mid_frame);
	$bot_row = $skin->tr($back_button . $mid_buff . $next_button);
	
	$everything = $skin->table($form_start . $form_hidden . $top_row . $mid_row . $form_end . $bot_row, "cellpadding='0' cellspacing='0' height='390'");

}


if ($step_num == 4) {

	// check mysql connection here

//	$adminname
//	$sql_passname
//	$sql_dbname
//	$sql_hostname


	$form_start = "<form action='installer.php' method='post' name='form'>";
	$form_hidden = "<input type='$hidden_type' name='step_num' value='5'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_adminname' value='$sql_adminname'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_passname' value='$sql_passname'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_dbname' value='$sql_dbname'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_hostname' value='$sql_hostname'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_prefix' value='$sql_prefix'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_overwrite' value='$sql_overwrite'>";
	$form_hidden .= "<input type='$hidden_type' name='root_directory' value='$root_directory'>";
	$form_hidden .= "<input type='$hidden_type' name='at_path' value='$at_path'>";
	$form_hidden .= "<input type='$hidden_type' name='host' value='$host'>";
	$form_hidden .= "<input type='$hidden_type' name='domain' value='$domain'>";
	$form_hidden .= "<input type='$hidden_type' name='cookies' value='$cookies'>";
	$form_end   = "</form>";

	if($error == 1){
		$load_this_function = "alert('Passwords do not match, please retype passwords.');";
		// connection error message here
	}
	if($error == 2){
		$load_this_function = "alert('Username must be at least 5 characters.');";
		// connection error message here
	}



	// continue as normal if ok

	$head = $skin->p_title(". Admin information .");
	
	$top_row = $skin->th($head, "colspan='3'");
	$top_row = $skin->tr($top_row);
	
	$info_text = "Please enter a username and password that you will use to administer this web application. This will be used as the admin password... blah blah blah";
	$info_text .= "<hr size='1' width='100%' color='#000000' noshade>";
	$info_text = $skin->font($info_text);
	

	
	$user_input_text = $skin->font(". enter a desired user name .");
	$user_input = $skin->input("type=text size=32 name='username' value='$username'");
	$user_input = "$user_input_text<br>" . $user_input . "<br><br>";
	$user_input = $buffer_skin->td($user_input, "width='300' colspan='3'");
	$user_input = $buffer_skin->tr($user_input);
	
	$pass_input_text = $skin->font(". enter a desired password .");
	$pass_input = $skin->input("type=text size=32 name='password' value='$password'");
	$pass_input = "$pass_input_text<br>" . $pass_input . "<br><br>";
	$pass_input = $buffer_skin->td($pass_input, "width='300'");
	$pass_input = $buffer_skin->tr($pass_input);

	$pass2_input_text = $skin->font(". please re-enter your password .");
	$pass2_input = $skin->input("type=text size=32 name='passwordv' value='$passwordv'");
	$pass2_input = "$pass2_input_text<br>" . $pass2_input . "<br><br>";
	$pass2_input = $buffer_skin->td($pass2_input, "width='300'");
	$pass2_input = $buffer_skin->tr($pass2_input);
	
	
	$mid_row = $info_text . $user_input . $pass_input . $pass2_input;
	
	//----->
	$top_table = $buffer_skin->table($mid_row);
	$top_table = $skin->td($top_table, "colspan='3'");
	$top_table = $skin->tr($top_table);
	//<-----
		
	$nextText = $skin->font(". next .");
	$backText = $skin->font(". back .");
	$next_button = $skin->a($nextText, "href='#' onClick='form.submit()'");
	$back_button = $skin->a($backText, "href='installer.php?step_num=3&sql_overwrite=$sql_overwrite&sql_prefix=$sql_prefix&sql_adminname=$sql_adminname&sql_passname=$sql_passname&sql_dbname=$sql_dbname&sql_hostname=$sql_hostname&username=$username&password=$password&passwordv=$passwordv&root_directory=$root_directory&at_path=$at_path&host=$host&domain=$domain&cookies=$cookies'");
	
	$next_button = $buffer_skin->td($next_button, "width='100%' align='center'");
	$next_button = $buffer_skin->tr($next_button, $extra);
	$next_button = $buffer_skin->table($next_button, "cellpadding='0' cellspacing='0' width='100%' height='100%'");
	
	$back_button = $buffer_skin->td($back_button, "width='100%' align='center'");
	$back_button = $buffer_skin->tr($back_button, $extra);
	$back_button = $buffer_skin->table($back_button, "cellpadding='0' cellspacing='0' width='100%' height='100%'");
	
	$next_button = $skin->td($next_button, "width='100' height='25' align='center'");
	$back_button = $skin->td($back_button, "width='100' height='25' align='center'");
	$mid_buff = $skin->td("", "width='100' height='25'");
	
	$bot_row = $skin->tr($back_button . $mid_buff . $next_button);
	
	$everything= $skin->table($form_start . $form_hidden . $top_row . $top_table . $form_end . $bot_row, "cellpadding='0' cellspacing='0' height=390");
}


if ($step_num == 5) {
	
	$form_start = "<form action='installer.php' method='post' name='form'>";
	$form_hidden = "<input type='$hidden_type' name='step_num' value='6'>";
	$form_hidden .= "<input type='$hidden_type' name='username' value='$username'>";
	$form_hidden .= "<input type='$hidden_type' name='password' value='$password'>";
	$form_hidden .= "<input type='$hidden_type' name='passwordv' value='$passwordv'>";
	$form_hidden .= "<input type='$hidden_type' name='root_directory' value='$root_directory'>";
	$form_hidden .= "<input type='$hidden_type' name='at_path' value='$at_path'>";
	$form_hidden .= "<input type='$hidden_type' name='host' value='$host'>";
	$form_hidden .= "<input type='$hidden_type' name='domain' value='$domain'>";
	$form_hidden .= "<input type='$hidden_type' name='cookies' value='$cookies'>";
	$form_end   = "</form>";

	if(!$sql_adminname){
		$sql_adminname = ADMIN;
	}
	if(!$sql_passname){
		$sql_passname = PASS;
	}
	if(!$sql_hostname){
		$sql_hostname = HOST;
	}
	if(!$sql_dbname){
		$sql_dbname = DATABASENAME;
	}
	if(!$sql_prefix){
		$sql_prefix = PREFIX;
	}
	if($password != $passwordv){
			$loc = "Location: " . $_SERVER['PHP_SELF'] . "?step_num=4&error=1&sql_overwrite=$sql_overwrite&sql_prefix=$sql_prefix&sql_adminname=$sql_adminname&sql_passname=$sql_passname&sql_dbname=$sql_dbname&sql_hostname=$sql_hostname&username=$username&password=$password&passwordv=$passwordv&root_directory=$root_directory&at_path=$at_path&host=$host&domain=$domain&cookies=$cookies";
			header($loc);
	}
	if(strlen($username) < 5){
			$loc = "Location: " . $_SERVER['PHP_SELF'] . "?step_num=4&error=2&sql_overwrite=$sql_overwrite&sql_prefix=$sql_prefix&sql_adminname=$sql_adminname&sql_passname=$sql_passname&sql_dbname=$sql_dbname&sql_hostname=$sql_hostname&username=$username&password=$password&passwordv=$passwordv&root_directory=$root_directory&at_path=$at_path&host=$host&domain=$domain&cookies=$cookies";
			header($loc);
	}

	$head = $skin->p_title(". SQL information .");
	if($error == 1){
		$load_this_function = "alert('Could not connect to mysql.\\n\\nPlease check your username, password, and host.');";
		// connection error message here
	}
	if($error == 2){
		$load_this_function = "alert('Could not connect to database: $sql_dbname.\\n\\nPlease check your mysql database name.');";
		// connection error message here
	}
	if($error == 3){
		$load_this_function = "alert('MySQL table prefix \"$sql_prefix\" is already in use. Please choose another prefix.');";
		// connection error message here
	}
	$top_row = $skin->th($head, "colspan='3'");
	$top_row = $skin->tr($top_row);
	
	$admin_input_text = $skin->font("enter you SQL admin username");
	$admin_input = $skin->input("type=text size=32 name='sql_adminname' value='$sql_adminname'");
	$admin_input = "$admin_input_text<br>" . $admin_input . "<br><br>";
	$admin_input = $buffer_skin->td($admin_input, "width='300'");
	$admin_input = $skin->tr($admin_input);
		
	$sql_pass_input_text = $skin->font("enter your SQL admin password");
	$sql_pass_input = $skin->input("type=text size=32 name='sql_passname' value='$sql_passname'");
	$sql_pass_input = "$sql_pass_input_text<br>" . $sql_pass_input . "<br><br>";
	$sql_pass_input = $buffer_skin->td($sql_pass_input, "width='300'");
	$sql_pass_input = $skin->tr($sql_pass_input);
	
	$sql_db_input_text = $skin->font("enter you SQL database name");
	$sql_db_input = $skin->input("type=text size=32 name='sql_dbname' value='$sql_dbname'");
	$sql_db_input = "$sql_db_input_text<br>" . $sql_db_input . "<br><br>";
	$sql_db_input = $buffer_skin->td($sql_db_input, "width='300'");
	$sql_db_input = $skin->tr($sql_db_input);
	
	$sql_host_input_text = $skin->font("enter your SQL host (myhostname.com)");
	$sql_host_input = $skin->input("type=text size=32 name='sql_hostname' value='$sql_hostname'");
	$sql_host_input = "$sql_host_input_text<br>" . $sql_host_input . "<br><br>";
	$sql_host_input = $buffer_skin->td($sql_host_input, "width='300'");
	$sql_host_input = $skin->tr($sql_host_input);
	
	$sql_prefix_input_text = $skin->font("enter your desired avalanche prefix");
	$sql_prefix_input = $skin->input("type=text size=32 name='sql_prefix' value='$sql_prefix'");
	if($sql_overwrite){
		$temp = "CHECKED";
	}
	$sql_prefix_checkbox = $skin->check("name='sql_overwrite' value='1' $temp");
	$sql_prefix_checkbox_explain = $skin->font("Overwrite tables if exist");
	$sql_prefix_input = "$sql_prefix_input_text<br>" . "$sql_prefix_input<br>" . $sql_prefix_checkbox . " $sql_prefix_checkbox_explain<br>";
	$sql_prefix_input = $buffer_skin->td($sql_prefix_input, "width='300'");
	$sql_prefix_input = $skin->tr($sql_prefix_input);
	
	$bot_row = $form_start . $form_hidden . $admin_input . $sql_pass_input . $sql_db_input . $sql_host_input . $sql_prefix_input;
	
	//----->
	$bot_table .= $form_end;
	$bot_table = $buffer_skin->table($bot_row);
	$bot_table = $skin->td($bot_table, "colspan='3'");
	$bot_table = $skin->tr($bot_table);
	//<-----
	
	$nextText = $skin->font(". next .");
	$backText = $skin->font(". back .");
	$next_button = $skin->a($nextText, "href='#' onClick='form.submit()'");
	$back_button = $skin->a($backText, "href='installer.php?step_num=4&sql_overwrite=$sql_overwrite&sql_prefix=$sql_prefix&sql_adminname=$sql_adminname&sql_passname=$sql_passname&sql_dbname=$sql_dbname&sql_hostname=$sql_hostname&username=$username&password=$password&passwordv=$passwordv&root_directory=$root_directory&at_path=$at_path&host=$host&domain=$domain&cookies=$cookies'");
	
	$next_button = $buffer_skin->td($next_button, "width='100%' align='center'");
	$next_button = $buffer_skin->tr($next_button, $extra);
	$next_button = $buffer_skin->table($next_button, "cellpadding='0' cellspacing='0' width='100%' height='100%'");
	
	$back_button = $buffer_skin->td($back_button, "width='100%' align='center'");
	$back_button = $buffer_skin->tr($back_button, $extra);
	$back_button = $buffer_skin->table($back_button, "cellpadding='0' cellspacing='0' width='100%' height='100%'");
	
	$next_button = $skin->td($next_button,"width='100' height='25' align='center'");
	$back_button = $skin->td($back_button, "width='100' height='25' align='center'");
	$mid_buff = $skin->td("", "width='100' height='25'");
	$but_row = $skin->tr($back_button . $mid_buff . $next_button);
	
	$everything= $skin->table($top_row . $bot_table . $but_row, "cellpadding='0' cellspacing='0' height=390");

}

if ($step_num == 6) {
	$dbcnx = @mysql_connect($sql_hostname, $sql_adminname, $sql_passname); 
	if (!$dbcnx) { 
	// reload previous step with an error if test failed	
			$loc = "Location: " . $_SERVER['PHP_SELF'] . "?step_num=5&error=1&sql_overwrite=$sql_overwrite&sql_prefix=$sql_prefix&sql_adminname=$sql_adminname&sql_passname=$sql_passname&sql_dbname=$sql_dbname&sql_hostname=$sql_hostname&root_directory=$root_directory&at_path=$at_path&host=$host&domain=$domain&cookies=$cookies&username=$username&password=$password&passwordv=$passwordv";
			header($loc);
	}else{
		if(!mysql_select_db($sql_dbname,$dbcnx)){
			$loc = "Location: " . $_SERVER['PHP_SELF'] . "?step_num=5&error=2&sql_overwrite=$sql_overwrite&sql_prefix=$sql_prefix&sql_adminname=$sql_adminname&sql_passname=$sql_passname&sql_dbname=$sql_dbname&sql_hostname=$sql_hostname&root_directory=$root_directory&at_path=$at_path&host=$host&domain=$domain&cookies=$cookies&username=$username&password=$password&passwordv=$passwordv";
			header($loc);
		}
	}


	$form_start = "<form action='installer.php' method='post' name='form'>";
	$form_hidden = "<input type='$hidden_type' name='step_num' value='7'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_adminname' value='$sql_adminname'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_passname' value='$sql_passname'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_dbname' value='$sql_dbname'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_hostname' value='$sql_hostname'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_prefix' value='$sql_prefix'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_overwrite' value='$sql_overwrite'>";
	$form_hidden .= "<input type='$hidden_type' name='username' value='$username'>";
	$form_hidden .= "<input type='$hidden_type' name='password' value='$password'>";
	$form_hidden .= "<input type='$hidden_type' name='passwordv' value='$passwordv'>";
	$form_hidden .= "<input type='$hidden_type' name='root_directory' value='$root_directory'>";
	$form_hidden .= "<input type='$hidden_type' name='at_path' value='$at_path'>";
	$form_hidden .= "<input type='$hidden_type' name='host' value='$host'>";
	$form_hidden .= "<input type='$hidden_type' name='domain' value='$domain'>";
	$form_hidden .= "<input type='$hidden_type' name='cookies' value='$cookies'>";
	$form_end = "</form>";	

	$show = false;

	include 'mysql.php';
	if(!tablesAreOk()){
		$loc = "Location: " . $_SERVER['PHP_SELF'] . "?step_num=5&error=3&sql_overwrite=$sql_overwrite&sql_prefix=$sql_prefix&sql_adminname=$sql_adminname&sql_passname=$sql_passname&sql_dbname=$sql_dbname&sql_hostname=$sql_hostname&root_directory=$root_directory&at_path=$at_path&host=$host&domain=$domain&cookies=$cookies&username=$username&password=$password&passwordv=$passwordv";
		header($loc);
	}

	$head = $skin->p_title(". creating mysql tables .");
	$check_frame = "<iframe src='mysql.php?show=1&sql_overwrite=$sql_overwrite&sql_prefix=$sql_prefix&sql_adminname=$sql_adminname&sql_passname=$sql_passname&sql_dbname=$sql_dbname&sql_hostname=$sql_hostname&username=$username&password=$password' frameborder='0' width='300' height='310' name='checkframe'>browser must support iframes for installation to occur</iframe>";
	
	$mid_frame = $skin->td($check_frame, "width='300' colspan='3'");
	
	
	$nextText = $skin->font(". next .");
	$backText = $skin->font(". back .");
	$next_button = $skin->a($nextText, "href='#' onClick='form.submit()'");
	if(!tablesAreOk()){
		$next_button = "";
	}
	$back_button = $skin->a($backText, "href='installer.php?step_num=5&sql_overwrite=$sql_overwrite&sql_prefix=$sql_prefix&sql_adminname=$sql_adminname&sql_passname=$sql_passname&sql_dbname=$sql_dbname&sql_hostname=$sql_hostname&username=$username&password=$password&passwordv=$passwordv&root_directory=$root_directory&at_path=$at_path&host=$host&domain=$domain&cookies=$cookies'");



	$next_button = $buffer_skin->td($next_button, "width='100%' align='center'");
	$next_button = $buffer_skin->table($next_button, "cellpadding='0' cellspacing='0' width='100%' height='100%'");
	
	$back_button = $buffer_skin->td($back_button, "width='100%' align='center'");
	$back_button = $buffer_skin->tr($back_button, $extra);
	$back_button = $buffer_skin->table($back_button, "cellpadding='0' cellspacing='0' width='100%' height='100%'");
	
	$next_button = $skin->td($next_button, "width='100' height='25' align='center'");
	$back_button = $skin->td($back_button, "width='100' height='25' align='center'");
	$mid_buff = $skin->td("", "width='100' height='25'");
		
	$top_row = $skin->th($head, "colspan='3'");
	$top_row = $skin->tr($top_row);
	$mid_row = $skin->tr($mid_frame);
	$bot_row = $skin->tr($back_button . $mid_buff . $next_button);
	
	$everything = $skin->table($form_start . $form_hidden . $top_row . $mid_row . $form_end . $bot_row, "cellpadding='0' cellspacing='0' height='390'");

}


if ($step_num == 7) {
	
	$form_start = "<form action='installer.php' method='post' name='form'>";
	$form_hidden = "<input type='$hidden_type' name='step_num' value='8'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_adminname' value='$sql_adminname'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_passname' value='$sql_passname'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_dbname' value='$sql_dbname'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_hostname' value='$sql_hostname'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_prefix' value='$sql_prefix'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_overwrite' value='$sql_overwrite'>";
	$form_hidden .= "<input type='$hidden_type' name='username' value='$username'>";
	$form_hidden .= "<input type='$hidden_type' name='password' value='$password'>";
	$form_hidden .= "<input type='$hidden_type' name='passwordv' value='$passwordv'>";
	$form_end   = "</form>";

	$head = $skin->p_title(". set up host information .");
	$top_row = $skin->th($head, "colspan='3'");
	$top_row = $skin->tr($top_row);
	$info_text = "Please enter a username and password that you will use to administer this web application. This will be used as the admin password... blah blah blah";
	$info_text = $skin->font($info_text);
	

	if(!$root_directory){
		$root_directory = $_SERVER['DOCUMENT_ROOT'] . "/";
	}
	if(!$host){
		$host = "http://" . $_SERVER['SERVER_NAME'] . "/";
	}
	if(!$at_path){
		$at_path = substr($_SERVER['SCRIPT_NAME'], 1, strrpos($_SERVER['SCRIPT_NAME'], "/"));
	}
	if(!$domain){
		$domain = $_SERVER['HTTP_HOST'];
	}
	
	$dir_input_text = $skin->font(". enter root directory .");
	$dir_input = $skin->input("type=text size=32 name='root_directory' value='$root_directory'");
	$dir_input = "$dir_input_text<br>" . $dir_input . "<br><br>";
	$dir_input = $buffer_skin->td($dir_input, "width='300'");
	$dir_input = $buffer_skin->tr($dir_input);
	
	$host_input_text = $skin->font(". enter host URL .");
	$host_input = $skin->input("type=text size=32 name='host' value='$host'");
	$host_input = "$host_input_text<br>" . $host_input . "<br><br>";
	$host_input = $buffer_skin->td($host_input, "width='300'");
	$host_input = $buffer_skin->tr($host_input);
	
	$at_path_input_text = $skin->font(". enter at_path .");
	$at_path_input = $skin->input("type=text size=32 name='at_path' value='$at_path'");
	$at_path_input = "$at_path_input_text<br>" . $at_path_input . "<br><br>";
	$at_path_input = $buffer_skin->td($at_path_input, "width='300'");
	$at_path_input = $buffer_skin->tr($at_path_input);
	
	$domain_input_text = $skin->font(". enter domain .");
	$domain_input = $skin->input("type=text size=32 name='domain' value='$domain'");
	$domain_input = "$domain_input_text<br>" . $domain_input . "<br><br>";
	$domain_input = $buffer_skin->td($domain_input, "width='300'");
	$domain_input = $buffer_skin->tr($domain_input);
	
	$cookies_text = $skin->font(". cookies secure? .&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
	$cookies_drop_yes = $skin->option("1", "yes");
	$cookies_drop_no = $skin->option("0", "no");
	$cookies_drop = $skin->select($cookies_drop_yes . $cookies_drop_no, "name='cookies'");
	$cookies_drop = $buffer_skin->td($cookies_text . $cookies_drop, "width='300'");
	$cookies_drop = $buffer_skin->tr($cookies_drop);
	
	
	$nextText = $skin->font(". next .");
	$backText = $skin->font(". back .");
	$next_button = $skin->a($nextText, "href='#' onClick='form.submit()'");
	$back_button = $skin->a($backText, "href='installer.php?step_num=6&sql_overwrite=$sql_overwrite&sql_prefix=$sql_prefix&sql_adminname=$sql_adminname&sql_passname=$sql_passname&sql_dbname=$sql_dbname&sql_hostname=$sql_hostname&username=$username&password=$password&passwordv=$passwordv&root_directory=$root_directory&at_path=$at_path&host=$host&domain=$domain&cookies=$cookies'");
	
	$next_button = $buffer_skin->td($next_button, "width='100%' align='center'");
	$next_button = $buffer_skin->tr($next_button, $extra);
	$next_button = $buffer_skin->table($next_button, "cellpadding='0' cellspacing='0' width='100%' height='100%'");
	
	$back_button = $buffer_skin->td($back_button, "width='100%' align='center'");
	$back_button = $buffer_skin->tr($back_button, $extra);
	$back_button = $buffer_skin->table($back_button, "cellpadding='0' cellspacing='0' width='100%' height='100%'");
	$mid_buff = $skin->td("", "width='100' height='25'");
	
	$next_button = $skin->td($next_button, "width='100' height='25' align='center'");
	$back_button = $skin->td($back_button, "width='100' height='25' align='center'");
	$bot_row = $skin->tr($back_button . $mid_buff . $next_button);
	

	$mid_row = $skin->tr($skin->td($buffer_skin->table($dir_input . $host_input . $at_path_input . $domain_input . $cookies_drop), "colspan='3'"));
	
	$everything = $skin->table($form_start . $top_row . $mid_row . $form_hidden . $form_end . $bot_row, "cellpadding='0' cellspacing='0' height=390");
	
}


if ($step_num == 8) {

	$form_start = "<form action='installer.php' method='post' name='form'>";
	$form_hidden = "<input type='$hidden_type' name='step_num' value='9'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_adminname' value='$sql_adminname'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_passname' value='$sql_passname'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_dbname' value='$sql_dbname'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_hostname' value='$sql_hostname'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_prefix' value='$sql_prefix'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_overwrite' value='$sql_overwrite'>";
	$form_hidden .= "<input type='$hidden_type' name='username' value='$username'>";
	$form_hidden .= "<input type='$hidden_type' name='password' value='$password'>";
	$form_hidden .= "<input type='$hidden_type' name='passwordv' value='$passwordv'>";
	$form_hidden .= "<input type='$hidden_type' name='root_directory' value='$root_directory'>";
	$form_hidden .= "<input type='$hidden_type' name='at_path' value='$at_path'>";
	$form_hidden .= "<input type='$hidden_type' name='host' value='$host'>";
	$form_hidden .= "<input type='$hidden_type' name='domain' value='$domain'>";
	$form_hidden .= "<input type='$hidden_type' name='cookies' value='$cookies'>";

	$form_end   = "</form>";

	
	$head = $skin->p_title(". include.avalanche.fullapp.php .");
	$top_row = $skin->th($head, "colspan='3'");
	$top_row = $skin->tr($top_row);
	$info_text = "To complete installation, save the following text as \"include.avalanche.fullapp.php\" and upload the file to the same directory as this installation file.";
	$info_text = $skin->font($info_text);
	$info_text .= "<hr size='1' width='100%' color='#000000' noshade>";


	$str = "&lt;?php\n";
	$str .= "if(! defined(avalanche_FULLAPP_PHP)){\n";
	$str .= " define(ROOT, \"$root_directory\");\n";
	$str .= " define(HOSTURL, \"$host\");\n";
	$str .= " define(DOMAIN, \"$domain\");\n";
	$str .= " define(APPPATH, \"$at_path\");\n";
	$str .= " \n";
	$str .= " //set SECURE to 1 if cookies need to be sent over https connection\n";
	$str .= " define(SECURE, \"$cookies\");\n";
	$str .= " define(INCLUDEPATH, \"includes/\");\n";
	$str .= " define(MODULES, \"modules/\");\n";
	$str .= " define(SKINS, \"skins/\");\n";
	$str .= " define(HOST, \"$sql_hostname\");\n";
	$str .= " define(ADMIN, \"$sql_adminname\");\n";
	$str .= " define(PASS, \"$sql_passname\");\n";
	$str .= " define(DATABASENAME, \"$sql_dbname\");\n";
	$str .= " define(PREFIX, \"$sql_prefix\");\n";
	$str .= " include ROOT . APPPATH . INCLUDEPATH . \"include.php\";\n";
	$str .= "}\n";
	$str .= "?>\n";

	$str = $skin->textarea($str, "cols='55' rows='20' wrap='off' READONLY");

	$nextText = $skin->font(". next .");
	$backText = $skin->font(". back .");
	$next_button = $skin->a($nextText, "href='#' onClick='form.submit()'");
	$back_button = $skin->a($backText, "href='installer.php?step_num=7&sql_overwrite=$sql_overwrite&sql_prefix=$sql_prefix&sql_adminname=$sql_adminname&sql_passname=$sql_passname&sql_dbname=$sql_dbname&sql_hostname=$sql_hostname&username=$username&password=$password&passwordv=$passwordv&root_directory=$root_directory&at_path=$at_path&host=$host&domain=$domain&cookies=$cookies'");
	
	$next_button = $buffer_skin->td($next_button, "width='100%' align='center'");
	$next_button = $buffer_skin->tr($next_button, $extra);
	$next_button = $buffer_skin->table($next_button, "cellpadding='0' cellspacing='0' width='100%' height='100%'");
	
	$back_button = $buffer_skin->td($back_button, "width='100%' align='center'");
	$back_button = $buffer_skin->tr($back_button, $extra);
	$back_button = $buffer_skin->table($back_button, "cellpadding='0' cellspacing='0' width='100%' height='100%'");
	$mid_buff = $skin->td("", "width='100' height='25'");
	
	$next_button = $skin->td($next_button, "width='100' height='25' align='center'");
	$back_button = $skin->td($back_button, "width='100' height='25' align='center'");
	$bot_row = $skin->tr($back_button . $mid_buff . $next_button);

	$mid_row = $skin->tr($skin->td($buffer_skin->table($buffer_skin->tr($buffer_skin->td($info_text . "<center>" . $str . "</center>", "width='300'")), "cellpadding='0' cellspacing='0'"), "colspan='3'"));

	$everything = $skin->table($form_start . $form_hidden . $top_row . $mid_row . $form_end . $bot_row, "cellpadding='0' cellspacing='0' height=390");

}

if ($step_num == 9) {

	$form_start = "<form action='installer.php' method='post' name='form'>";
	$form_hidden = "<input type='$hidden_type' name='step_num' value='10'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_adminname' value='$sql_adminname'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_passname' value='$sql_passname'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_dbname' value='$sql_dbname'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_hostname' value='$sql_hostname'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_prefix' value='$sql_prefix'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_overwrite' value='$sql_overwrite'>";
	$form_hidden .= "<input type='$hidden_type' name='username' value='$username'>";
	$form_hidden .= "<input type='$hidden_type' name='password' value='$password'>";
	$form_hidden .= "<input type='$hidden_type' name='passwordv' value='$passwordv'>";
	$form_hidden .= "<input type='$hidden_type' name='root_directory' value='$root_directory'>";
	$form_hidden .= "<input type='$hidden_type' name='at_path' value='$at_path'>";
	$form_hidden .= "<input type='$hidden_type' name='host' value='$host'>";
	$form_hidden .= "<input type='$hidden_type' name='domain' value='$domain'>";
	$form_hidden .= "<input type='$hidden_type' name='cookies' value='$cookies'>";

	$form_end   = "</form>";

	
	$head = $skin->p_title(". Installation Complete .");
	$top_row = $skin->th($head, "colspan='3'");
	$top_row = $skin->tr($top_row);
	$info_text = "Installation is complete. To proceed to the Control Panel, click [Next] or select [View Readme] to learn more about phpAvalanche.";
	$info_text = $skin->font($info_text);
	$info_text .= "<hr size='1' width='100%' color='#000000' noshade>";

	$nextText = $skin->font(". next .");
	$backText = $skin->font(". back .");
	$next_button = $skin->a($nextText, "href='#' onClick='form.submit()'");
	$back_button = $skin->a($backText, "href='installer.php?step_num=8&sql_overwrite=$sql_overwrite&sql_prefix=$sql_prefix&sql_adminname=$sql_adminname&sql_passname=$sql_passname&sql_dbname=$sql_dbname&sql_hostname=$sql_hostname&username=$username&password=$password&passwordv=$passwordv&root_directory=$root_directory&at_path=$at_path&host=$host&domain=$domain&cookies=$cookies'");
	
	$next_button = $buffer_skin->td($next_button, "width='100%' align='center'");
	$next_button = $buffer_skin->tr($next_button, $extra);
	$next_button = $buffer_skin->table($next_button, "cellpadding='0' cellspacing='0' width='100%' height='100%'");
	
	$back_button = $buffer_skin->td($back_button, "width='100%' align='center'");
	$back_button = $buffer_skin->tr($back_button, $extra);
	$back_button = $buffer_skin->table($back_button, "cellpadding='0' cellspacing='0' width='100%' height='100%'");
	$mid_buff = $skin->td("", "width='100' height='25'");
	
	$next_button = $skin->td($next_button, "width='100' height='25' align='center'");
	$back_button = $skin->td($back_button, "width='100' height='25' align='center'");
	$bot_row = $skin->tr($back_button . $mid_buff . $next_button);

	$readme_checkbox = $skin->check("name='readme' value='1'");
	$readme_checkbox_explain = $skin->font(" View Readme file");
	$readme_input = "<br><br><br>";
	$readme_input .= $readme_checkbox . " $readme_checkbox_explain<br>";
	$readme_input .= "<br><br><br><br><br><br><br>";
	$readme_input = $buffer_skin->td($readme_input, "width='300' align='center'");
	$readme_input = $skin->tr($readme_input);

	$mid_row = $skin->tr($skin->td($buffer_skin->table($buffer_skin->tr($buffer_skin->td($info_text . $readme_input, "width='300'")), "cellpadding='0' cellspacing='0'"), "colspan='3'"));

	$everything = $skin->table($form_start . $form_hidden . $top_row . $mid_row . $form_end . $bot_row, "cellpadding='0' cellspacing='0' height=390");

}

if ($step_num == 10) {

	if(!$readme){
		$loc = "Location: " . $host . $at_path . "command.php";
		header($loc);
	}


	$form_start = "<form action='installer.php' method='post' name='form'>";
	$form_hidden = "<input type='$hidden_type' name='step_num' value='11'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_adminname' value='$sql_adminname'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_passname' value='$sql_passname'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_dbname' value='$sql_dbname'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_hostname' value='$sql_hostname'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_prefix' value='$sql_prefix'>";
	$form_hidden .= "<input type='$hidden_type' name='sql_overwrite' value='$sql_overwrite'>";
	$form_hidden .= "<input type='$hidden_type' name='username' value='$username'>";
	$form_hidden .= "<input type='$hidden_type' name='password' value='$password'>";
	$form_hidden .= "<input type='$hidden_type' name='passwordv' value='$passwordv'>";
	$form_hidden .= "<input type='$hidden_type' name='root_directory' value='$root_directory'>";
	$form_hidden .= "<input type='$hidden_type' name='at_path' value='$at_path'>";
	$form_hidden .= "<input type='$hidden_type' name='host' value='$host'>";
	$form_hidden .= "<input type='$hidden_type' name='domain' value='$domain'>";
	$form_hidden .= "<input type='$hidden_type' name='cookies' value='$cookies'>";

	$form_end   = "</form>";

	
	$head = $skin->p_title(". The Readme .");
	$top_row = $skin->th($head, "colspan='3'");
	$top_row = $skin->tr($top_row);
	$info_text = "Browse the readme to learn more about phpAvalanche.";
	$info_text = $skin->font($info_text);
	$info_text .= "<hr size='1' width='100%' color='#000000' noshade>";


	$str = array();
	$str = file("readme.txt");
	$str = implode($str, "\n");

	$str = $skin->textarea($str, "cols='55' rows='20' wrap='physical' READONLY");

	$nextText = $skin->font(". finish .");
	$backText = $skin->font(". back .");
	$next_button = $skin->a($nextText, "href='#'");
	$back_button = $skin->a($backText, "href='installer.php?step_num=9&sql_overwrite=$sql_overwrite&sql_prefix=$sql_prefix&sql_adminname=$sql_adminname&sql_passname=$sql_passname&sql_dbname=$sql_dbname&sql_hostname=$sql_hostname&username=$username&password=$password&passwordv=$passwordv&root_directory=$root_directory&at_path=$at_path&host=$host&domain=$domain&cookies=$cookies'");
	
	$next_button = $buffer_skin->td($next_button, "width='100%' align='center'");
	$next_button = $buffer_skin->tr($next_button, $extra);
	$next_button = $buffer_skin->table($next_button, "cellpadding='0' cellspacing='0' width='100%' height='100%'");
	
	$back_button = $buffer_skin->td($back_button, "width='100%' align='center'");
	$back_button = $buffer_skin->tr($back_button, $extra);
	$back_button = $buffer_skin->table($back_button, "cellpadding='0' cellspacing='0' width='100%' height='100%'");
	$mid_buff = $skin->td("", "width='100' height='25'");
	
	$next_button = $skin->td($next_button, "width='100' height='25' align='center'");
	$back_button = $skin->td($back_button, "width='100' height='25' align='center'");
	$bot_row = $skin->tr($back_button . $mid_buff . $next_button);

	$mid_row = $skin->tr($skin->td($buffer_skin->table($buffer_skin->tr($buffer_skin->td($info_text . "<center>" . $str . "</center>", "width='300'")), "cellpadding='0' cellspacing='0'"), "colspan='3'"));

	$everything = $skin->table($form_start . $form_hidden . $top_row . $mid_row . $form_end . $bot_row, "cellpadding='0' cellspacing='0' height=390");
}


echo "<html>";
echo "<head>";
echo $skin->header();
echo "<title>. installer . inversiondesigns . com .</title>";
echo "</head>";
echo "<body background='../scans2.gif' onLoad='loadthis()'>";
echo "<script>";
echo "function loadthis(){\n";
echo $load_this_function;
echo "\n}";
echo "</script>";
echo $skin->javascript();
echo "<center>";
echo $everything;
echo "</center>";
echo "</body>";
echo "</html>";


