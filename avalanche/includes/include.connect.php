<?php

	$avalanche_db = mysqli_connect(HOST, ADMIN, PASS);
	mysql_select_db(DATABASENAME,$avalanche_db);


function getDb(){
	$tableName = PREFIX . $tableName;
	$db = mysqli_connect(HOST, ADMIN, PASS);
	mysql_select_db(DATABASENAME,$db);
	return $db;
}



function runSQL($sql){
	global $avalanche_db;
	$result = mysqli_query($sql,$avalanche_db);
	return $result;
}


//connnects to a table and returns the result of a query with or without a where statement.
function connectTo($tableName, $where="none"){

	$tableName = PREFIX . $tableName;
	$db = mysqli_connect(HOST, ADMIN, PASS);
	mysql_select_db(DATABASENAME,$db);

	if($where=="none"){
		$sql = "SELECT * FROM " . $tableName;
	}else{
		$sql = "SELECT * FROM " . $tableName . " WHERE " . $where;
	}

		$result = mysqli_query($sql,$db);

	return $result;

}


//gets a value from a table.
//$table = the table to get the value from
//$col = the column of the $table to get the value from
//$Id = the value to look for in the $colForId column
function get($table, $col, $Id, $colForId){
	$result = connectTo($table, $colForId . "='" . $Id . "'");
	while ($myrow = mysqli_fetch_array($result)) {
		if($Id == $myrow[$colForId]){  // just in case. should always be true
			return $myrow[$col];
		}
	}
}


function insert($tableName, $vars, $vals){
	$tableName = PREFIX . $tableName;
	$db = mysqli_connect(HOST, ADMIN, PASS);
	mysql_select_db(DATABASENAME,$db);

        $sql = "INSERT INTO $tableName $vars VALUES $vals";

        $result = mysqli_query($sql, $db);
	return $result;
}

function countRows($tableName, $where=0){

	$tableName = PREFIX . $tableName;
	$db = mysqli_connect(HOST, ADMIN, PASS);
	mysql_select_db(DATABASENAME,$db);

	if(!$where){
		$sql = "SELECT * FROM " . $tableName;
	}else{
		$sql = "SELECT * FROM " . $tableName . " WHERE " . $where;
	}


	$result = mysqli_query($sql,$db);


	$count = mysqli_num_rows($result);

	return $count;
}

function delete($tableName, $where=0){
	$sql = "";
	$tableName = PREFIX . $tableName;
	$db = mysqli_connect(HOST, ADMIN, PASS);
	$result = mysqli_select_db(DATABASENAME,$db);

	if(!$where){
		$sql = "DELETE FROM " . $tableName;
	}else{
		$sql = "DELETE FROM " . $tableName . " WHERE " . $where;
	}

	$result = mysqli_query($sql, $db);

	return $result;
}

?>
