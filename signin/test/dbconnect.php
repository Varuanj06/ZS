<?php 
	require_once("dbconfig.php");
	
	global $conn;
	global $conn_reports;
    global $conn_prestashop;
	
    try {
   
    	$conn = new PDO("mysql:host=". $config['host']  .";dbname=". $config['db'], $config['user'] , $config['pass'] );
    	$conn->exec("set names utf8");
    	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $conn_reports = new PDO("mysql:host=". $config_reports['host']  .";dbname=". $config_reports['db'], $config_reports['user'] , $config_reports['pass'] );
        $conn_reports->exec("set names utf8");
        $conn_reports->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    	$conn_prestashop = new PDO("mysql:host=". $config_prestashop['host']  .";dbname=". $config_prestashop['db'], $config_prestashop['user'] , $config_prestashop['pass'] );
    	$conn_prestashop->exec("set names utf8");
    	$conn_prestashop->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   
    } catch(PDOException $e) {
	    echo 'ERROR: ' . $e->getMessage();
	}
?>