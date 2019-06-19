<?php
	echo '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
	$error = 0;	
	
	require_once 'config.php';
	
	$dbhost = DB_HOSTNAME;
	$dbuser = DB_USERNAME;
	$dbpass = DB_PASSWORD;
	$dbdatabase = DB_DATABASE;
	$dbprefix = DB_PREFIX;
		
	$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbdatabase);
	if(!$conn ) die('Could not connect: ' . mysql_error());

	$sql = 'CREATE TABLE IF NOT EXISTS `' . $dbprefix . 'key`( '.
       'id INT NOT NULL AUTO_INCREMENT, '.
       'value text, '.
       'main_key  VARCHAR(256), '.
       'license_key text, '.       
       'primary key ( id ))';
	
	$retval = $conn->query($sql);
	if(!$retval ) die('Could not create table: ' . mysql_error());
	echo "Table key created successfully<br />";	
	$conn->close();

	$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbdatabase);
	$query = "select * from `" . $dbprefix . "key` where `main_key`='local_key'";	
	$retval = $conn->query($query);
	if(!$retval) die('Could not read table: ' . mysql_error());	
	$rows = $retval->fetch_assoc();
	if (empty($rows)) {
		$query = "INSERT INTO `" . $dbprefix . "key` SET `value` = '', `main_key` = 'local_key', `license_key` = ''";
		$query_res = $conn->query($query);
		if($query_res) echo " Open new license";
		else echo " Can not write to table 'key'";
	}
	$conn->close();
	if (!$error) echo " MODULE SUCCESSFULLY INSTALLED";
	else echo 'Please, check ' . $error . ' error(s)';
	

?>
