<?php
/*
  +----------------------------------------------------------------------+
  | MySQL/MySQLi compatibility layer                                     |
  +----------------------------------------------------------------------+
  | Copyright (c) 2003 John Coggeshall                                   |
  |                                                                      |
  | Licensed under V1.0 ofthe coggeshall.org license included in         |
  | this package or online at:                                           |
  |                                                                      |
  | http://www.coggeshall.org/oss/license.php                            |
  +----------------------------------------------------------------------+
  | Author: John Coggeshall <john@php.net>                               |
  +----------------------------------------------------------------------+
  
*/

/*

The point of this script is to provide a way for people to
run PHP4 scripts which use the standard mysql extension with
the new MySQLi extension. Using this compatiability layer
removes the need for having two MySQL extensions installed,
without having to modify the legacy code.

*/

if(!extension_loaded("mysql")) {
    
    if(!extension_loaded("mysqli")) {
        trigger_error("You must have the MySQLi extension.", E_USER_ERROR);
    }
    
    define("MYSQL_NUM", MYSQLI_NUM);
    define("MYSQL_ASSOC", MYSQLI_ASSOC);
    define("MYSQL_BOTH", MYSQLI_BOTH);
    
    define("MYSQL_CLIENT_COMPRESS", NULL);
    define("MYSQL_CLIENT_IGNORE_SPACE", NULL);
    define("MYSQL_CLIENT_INTERACTIVE", NULL);
    
    $__mysql = NULL;

    function mysql_not_implemented($function) {
        
        trigger_error("The $function function is not implemented in this compatibility library", E_USER_WARNING);
        return false;
        
    }
    
    function mysql_resolve_link($link) {
        global $__mysql;
        if($link == NULL) {
            if(!is_object($__mysql)) {
                trigger_error("A connection must be made to the database (invalid link handle)", E_USER_ERROR);
            } else {
                return $__mysql;
            }
        } 
        return $link;
    }
        
    function mysql_connect($host = NULL, $user = NULL, $pass = NULL, $new = false, $flags = 0) {
        
        global $__mysql;
        
        $extra_info = substr(strstr($host, ":"), 1);
        if(!empty($extra_info)) {
            
            if(is_numeric($extra_info)) {
                $port = $extra_info;
            } else {
                $socket = $extra_info;
            }
         
            $host = substr($host, 0, strlen($host)-(strlen($extra_info)+1));
            
        }
        
        if(empty($socket)) {
            
            if(empty($port)) {
             $__mysql = mysqli_connect($host, $user, $pass);
            } else {
             $__mysql = mysqli_connect($host, $user, $pass, NULL, $port);
            }
            
        } else {
            $__mysql = mysqli_connect($host, $user, $pass, NULL, NULL, $socket);
        }

        return $__mysql;
    }
    
    function mysql_select_db($dbname, $link = NULL) {
        $link = mysql_resolve_link($link);
        return mysqli_select_db($link, $dbname);
    }

    function mysql_query($query, $link = NULL) {
        $link = mysql_resolve_link($link);
        return mysqli_query($link, $query);
    }
    
    function mysql_fetch_assoc($result) {
        return mysqli_fetch_assoc($result);
    }
    
    function mysql_fetch_array($result) {
        return mysqli_fetch_array($result);
    }
    
    function mysql_fetch_field($result, $offset = NULL) {
        
        if($offset != NULL) {
            
            for($i = 0;
                ($i < $offset) ||
                !($retval = mysqli_fetch_field($result));
                $i++); /* Missing code intentionally */
                       
        } else {
            
            return mysqli_fetch_field($result);
            
        }
        
        return ($retval) ? $retval : false;
        
    }
    
    function mysql_close($link = NULL) {
        $link = mysql_resolve_link($link);
        return mysqli_close($link);
        
    }
    
    function mysql_affected_rows($link = NULL) {
        $link = mysql_resolve_link($link);
        return mysqli_affected_rows($link);
        
    }
    
    function mysql_change_user($user, $passwd, $database = NULL, $link = NULL) {
        $link = mysql_resolve_link($link);
        return mysqli_change_user($link, $user, $passwd, $database);
    }
    
    function mysql_client_encoding($link = NULL) {
        $link = mysql_resolve_link($link);
        return mysqli_character_set_name($link);
    }
    
    function mysql_create_db($database, $link = NULL) {
        $link = mysql_resolve_link($link);
        $query = "CREATE DATABASE $database";
        $result = msyqli_query($link, $query);
        return (!$result) ? false : true;
    }
    
    function mysql_data_seek($result, $row) {
        return mysqli_data_seek($result, $row);
    }
    
    function mysql_db_name($result, $row, $field = NULL) {
        return mysql_not_implemented("mysql_db_name()");
    }
    
    function mysql_db_query($database, $query, $link = NULL) {
        $link = mysql_resolve_link($link);
        mysqli_select_db($link, $database);
        return mysqli_query($link, $query);
    }
    
    function mysql_drop_db($dtabase, $link = NULL) {
        $link = mysql_resolve_link($link);
        $query = "DROP DATABASE $database";
        $result = mysqli_query($link, $query);
        return (!$result) ? false : true;
    }
    
    function mysql_error($link = NULL) {
        $link = mysql_resolve_link($link);
        return mysqli_error($link);
    }
    
    function mysql_errno($link = NULL) {
        $link = mysql_resolve_link($link);
        return mysqli_errno($link);
    }
    
    function mysql_escape_string($string) {
        $link = mysql_resolve_link(NULL);
        return mysqli_real_escape_string($link, $string);
    }
    
    function mysql_fetch_lengths($result) {
        return mysqli_fetch_lengths($result);
    }
    
    function mysql_fetch_object($result) {
        return mysqli_fetch_object($result);
    }
    
    function mysql_fetch_row($result) {
        return mysqli_fetch_row($result);
    }
    
    function mysql_field_flags($result, $offset) {
        return mysql_not_implemented("mysql_field_flags()");
    }
    
    function mysql_field_len($result, $offset) {
        return mysql_not_implemented("mysql_field_len()");
    }
    
    function mysql_field_name($result, $offset) {
        return mysql_not_implemented("mysql_field_name()");
    }
    
    function mysql_field_seek($result, $offset) {
        return mysqli_field_seek($resutl, $offset);
    }
    
    function mysql_field_table($result, $offset) {
        return mysql_not_implemented("mysql_field_table()");
    }
    
    function mysql_field_type($result, $offset) {
        return mysql_not_implemented("mysql_field_type()");
    }
    
    function mysql_free_result($result) {
        return mysqli_free_result($result);
    }
    
    function mysql_get_client_info() {
        return mysqli_get_client_info();
    }
    
    function mysql_get_host_info($link = NULL) {
        $link = mysql_resolve_link($link);
        return mysqli_get_client_info($link);
        
    }
    
    function mysql_get_proto_info($link = NULL) {
        $link = mysql_resolve_link($link);
        return mysqli_get_proto_info($link);
    }
    
    function mysql_get_server_info($link = NULL) {
        $link = mysql_resolve_link($link);
        return mysqli_get_server_info($link);
    }
    
    function mysql_info($link = NULL) {
        $link = mysql_resolve_link($link);
        return mysqli_info($link);
    }
    
    function mysql_insert_id($link = NULL) {
        $link = mysql_resolve_link($link);
        return mysqli_insert_id($link);
    }
    
    function mysql_list_dbs($link = NULL) {
        $link = mysql_resolve_link($link);
        $query = "SHOW DATABASES";
        return mysqli_query($link, $query);
    }
    
    function mysql_list_fields($database, $table, $link = NULL) {
        return mysql_not_implemented("mysql_list_fields()");
    }
    
    function mysql_list_processes($link = NULL) {
        return mysql_not_implemented("mysql_list_processes()");
    }
    
    function mysql_list_tables($database, $link = NULL) {
        return mysql_not_implemented("mysql_list_tables()");
    }
    
    function mysql_num_fields($result) {
        return mysqli_num_fields($result);
    }
    
    function mysql_num_rows($result) {
        return mysqli_num_rows($result);
    }
    
    function mysql_pconnect($host = NULL, $user = NULL, $passwd = NULL, $flags = NULL) {
        return mysql_connect($host, $user, $passwd, false, $flags);
    }
    
    function mysql_ping($link = NULL) {
	$link = mysql_resolve_link($link);
        return mysqli_ping($link);
    }
    
    function mysql_real_escape_string($string, $link = NULL) {
        $link = mysql_resolve_link($link);
        return mysqli_real_escape_stirng($link, $string);
    }
    
    function mysql_result($result, $row, $field = NULL) {
        return mysql_not_implemented("mysql_result()");
    }
   
    function mysql_stat($link = NULL) {
        $link = mysql_resolve_link($link);
        return mysqli_stat($link);
    }
    
    function mysql_tablename($result, $offset) {
        return mysql_not_implemented("mysql_tablename()");
    }
    
    function mysql_thread_id($link = NULL) {
        $link = mysql_resolve_link($link);
        return mysqli_thread_id($link);
    }
    
    function mysql_unbuffered_query($query, $link = NULL, $result_mode = NULL) {
        return mysql_not_implemented("mysql_unbuffered_query()");
    }
        
    
} /* End of extension_exists() test */
    
?>
