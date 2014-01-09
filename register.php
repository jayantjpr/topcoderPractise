<!DOCTYPE html>
<html>
<head>
	<title>Register</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body background="images.jpg">
<?php include_once "reg_process.php"; ?>
<form id="reg_form" class="center_form" method="post" action="register.php">
<table id="reg_table">
	<tr>
    	<td>Name : </td> 
    	<td><input type="text" id="name"  ></td>
	</tr>
	<br>
	<tr>
    	<td>Top-coder handle :</td> 
    	<td><input type="text" id="user_nm"></td>
	</tr>
	<br>
	<tr>
        <td>Profile :</td> 
        <td><input type="text" id="profile"></td>
	</tr>
	<br>
	<tr>
    	<td>Password :</td>
    	<td><input type="password" id="pass"></td>
	</tr>
	<br>
	<tr>
	    <td><input type="button" value="Register" id="register"></td>
	</tr>
	<br>
	<tr>
	    <td><a href="login.php"> Login Page </a></td>
	<tr>
</table>
</form>
</body>
</html>