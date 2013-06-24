<?php //common.php

	$db_dbname = 'clbase_development';
	$db_host = 'localhost';
	$db_username = 'root';
	$db_password = 'braindrain';

	//this options array is set to tell to tell the mysql connection we will only communicate in
	//UTF-8 encoding
	$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');

	try
	{
		$db = new PDO("mysql:host={$db_host};dbname={$db_dbname};charset=utf8", $db_username, $db_password, $options);
	}
	catch(PDOException $ex)
	{
		die("Failed to connect to the database: " . $ex->getMessage());
	}
	catch(Exception $generic_ex)
	{
		die("Error encountered: " . $generic_ex->getMessage());
	}

	//Tell PDO to throw exception when db error is encountered.
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	//tell PDO to return everything as an associative array with string indexes
	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

	//I have php version 5.4 installed currently.  not using this block
	// This block of code is used to undo magic quotes.  Magic quotes are a terrible 
    // feature that was removed from PHP as of PHP 5.4.  However, older installations 
    // of PHP may still have magic quotes enabled and this code is necessary to 
    // prevent them from causing problems.  For more information on magic quotes: 
    // http://php.net/manual/en/security.magicquotes.php 
    // if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) 
    // { 
    //     function undo_magic_quotes_gpc(&$array) 
    //     { 
    //         foreach($array as &$value) 
    //         { 
    //             if(is_array($value)) 
    //             { 
    //                 undo_magic_quotes_gpc($value); 
    //             } 
    //             else 
    //             { 
    //                 $value = stripslashes($value); 
    //             } 
    //         } 
    //     } 
     
    //     undo_magic_quotes_gpc($_POST); 
    //     undo_magic_quotes_gpc($_GET); 
    //     undo_magic_quotes_gpc($_COOKIE); 
    // }

	//let the browser know everything is going to be alright...and in UTF8
    header('Content-type: text/html; charset=utf-8');

    //start the session!
    session_start();

    function check_user_session()
    {
    	if(!isset($_SESSION['user_info']))
    	{
    		header("Location: login.php");
    		die("redirecting to: login.php");
    	}
    }

