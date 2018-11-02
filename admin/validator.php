<?php
	session_start();
	
	require_once("../dbconnect.php");
    
	require_once("../classes/user.php"); // class

	$user = new user();

	$user_val 		= $_POST['user'];	
	$password_val 	= $_POST['password'];

	$user->mapByUser($user_val);	

	// Do passwords match?
	if($user->getPassword() === $password_val){
		echo "correct";

		// Upload some session variables
		$_SESSION['userId-product-feed'] 	= $user->getUser_id();
		$_SESSION['user'] 					= $user->getUser();
		$_SESSION['name'] 					= $user->getName();
	}else{
		echo "nothing:";
	}


