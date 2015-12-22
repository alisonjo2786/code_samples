<?php
/*thank you to http://www.php.net/manual/en/function.mysql-real-escape-string.php#101248 !*/
function mysqlEscape($str) {
	if(is_array($str))
			return array_map(__METHOD__, $str);
	if(!empty($str) && is_string($str)) {
			return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $str);
	}
	return $str;
}
function storeData($sql,$eMore) {
	$eMore = "";
	$hn = 'localhost';
	$user = 'advocacy_user';
	$pw = 'password123';
	$cn = MySQL_connect($hn, $user, $pw);
	if ($cn) {
		$dbname = 'advocacy_db';
		$connect = MySQL_select_db($dbname);
		if ($connect) {
			$return = MySQL_query($sql,$cn);
			if ($return == TRUE) {
				//success...
			} else {
				$eMore .= "Query (return) was not successful.\n";
				//error...
			}
			MySQL_close($cn);
		} else {
			$eMore .= "DB selection was not successful.\n";
		}
	} else {
		$eMore .= "Connection to db was not successful.\n";
	}
		return $eMore;
}
?>