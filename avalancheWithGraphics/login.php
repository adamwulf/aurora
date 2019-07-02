<?

include "include.avalanche.fullApp.php";



if($logout && $avalanche->loggedInHuh()){
 $avalanche->logOut();
 header("Location: login.php");
}

if($login && $avalanche->needLogIn()){
 $avalanche->logIn($user,$pass);
 header("Location: login.php");
}



if($avalanche->loggedInHuh()){
	echo "<form action='login.php' method='post'>";
	echo "<input type='submit' name='logout' value='logout'>";
	echo "</form>";
}else{
	echo "<form action='login.php' method='post'>";
	echo "<input type='text' name='user' value=''><br>";
	echo "<input type='password' name='pass' value=''><br>";
	echo "<input type='submit' name='login' value='login'>";
	echo "</form>";
}




?>