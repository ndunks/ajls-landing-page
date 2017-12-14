<?php

session_start();
$config	= false;

if(is_file('config.php')){
	include 'config.php';
	
}

if(!$config && basename($_SERVER['REQUEST_URI']) != 'login.php'){
	header('Location: login.php');
	exit;
}