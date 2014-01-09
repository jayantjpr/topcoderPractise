<!DOCTYPE html>
<html>
<head>
	<title>Login</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body background="images.jpg">
<?php include_once "login_process.php"; ?>
<form id="login_form" class="center_form" method="post" action="login.php">
<table class="login_table" >
	<tr>
	<td>Top-coder handle :</td> 
	<td><input type="text" id="user_nm"></td>
	</tr>
	<br>
	<tr>
	<td>Password :</td>
	<td><input type="password" id="pass"></td>
	</tr>
	<br>
	<tr>
	<td><input type="button" value="Login" id="login"></td>
	</tr>
	<br>
	<tr>
	<td><a href="register.php"> Register </a></td>
	</tr>
</table>
</form>
</body>
</html>