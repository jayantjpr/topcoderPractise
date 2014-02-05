<?php

	// This starts the session which is like a cookie, but it isn't saved on your hdd and is much more secure.
	session_start();

	// That bit of code checks if you are logged in or not, and if you are, you can't log in again!
	if(isset($_SESSION['idr'])){
	    echo "You are already logged in! Redirecting you to correct page ...";
	    header("Location: ./index.php");
	    die();
	}

	include_once("generalclasses.php");

	// attempt a connection to PostgreSQL Database
	$dbh = new Database;
	if (!$dbh){
		die("Error in connection: " . pg_last_error());
	}

	if(isset($_POST['submit'])){
		$handle = trim($_POST['handle']);
		$password = md5(trim($_POST['password']));
		
		$result = pg_prepare($dbh -> getDB(), "login_query", 'SELECT * FROM registrants WHERE "handle" = $1 AND "password" = $2');
			// This code uses PostgreSQL to get all of the users in the database with that username and password.
			// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($dbh -> getDB(), "login_query", array($handle,$password));
		
		if(pg_num_rows($result) > 0){
			$row = pg_fetch_array($result);
			$_SESSION['idr'] = $row['idr']; // Set it so the user is logged in!
			if (isset($_SESSION['err'])) unset($_SESSION['err']);
		}
		else $_SESSION['err'] = "Incorrect Handle or Password!";

	    header("Location: ./index.php");
	    die();  // Kill the script here so it doesn't show the login form after you are logged in!
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Login</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body background="images.jpg">
	<div id="page-wrap">
        <ul class="breadcrumb">
            <li><a href="rules.php">Rules</a></li>
            <li><a href="register.php">Register</a></li>
           <li></li>
		</ul>
    </div>
<form id="login_form" class="center_form" method="post" action="login.php">
<table class="login_table" >
	<tr>
	<td>Top-coder handle :</td> 
	<td><input type="text" id="user_nm" name="handle"></td>
	</tr>
	<br>
	<tr>
	<td>Password :</td>
	<td><input type="password" id="pass" name="password"></td>
	</tr>
	<br>
	<tr>
	<td><input type="submit" value="Login" id="login" name="submit"></td>
	</tr>
	<br>
	<tr>
	<td><a href="./register.php"> Register </a></td>
	</tr>
	<tr>
	    <td>
	        <?php
	            if(isset($_SESSION['err'])) echo $_SESSION['err'];
	        ?>
	    </td>
	</tr>
</table>
</form>
</body>
</html>