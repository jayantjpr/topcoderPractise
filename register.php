<?php

  session_start(); // NEVER forget this!
  if(isset($_SESSION['idr'])){
      echo "You are already logged in and donot need to Register! Redirecting you to correct page ...";
      header("Location: ./index.php");
      die();
  }

  include_once("generalclasses.php");
  include_once("registrantclass.php");
          

  if(isset($_POST['submit'])){
    
    if(isset($_POST['handle']) && isset($_POST['name']) && isset($_POST['profile']) && isset($_POST['password'])){
      $handle=trim($_POST['handle']);
      $name=trim($_POST['name']);
      $profile=trim($_POST['profile']);
      $password=trim($_POST['password']);

      if (!empty($handle) && !empty($name) && !empty($profile) && !empty($password)){
       
        //get idr from profile
        $parts = parse_url($profile);
        if ($parts == FALSE || $parts['host'] != 'community.topcoder.com'){
          $_SESSION['reg_err'] = "Invalid Profile :(";
          header("Location: ./register.php");
          die();
        }
        parse_str($parts['query'], $query);
        $idr=$query['cr'];
        if ($idr == "0"){
          $_SESSION['reg_err'] = "Invalid Profile :(";
          header("Location: ./register.php");
          die();
        }
        $password = md5($password);
        
        // attempt a connection to PostgreSQL Database
      
        $dbh = new Database;
        if (!$dbh){
          die("Error in connection: " . pg_last_error());
        }

        $registrant = new Registrant;
        $registrant -> setId($idr);
        $registrant -> setHandle($handle);
        $registrant -> setName($name);
        $registrant -> setPassword($password);
      
        $result = $registrant -> insert($dbh);
        if(!$result) $_SESSION['reg_err'] = "Already Registered";
        else
          if (isset($_SESSION['reg_err'])) unset($_SESSION['reg_err']);

        header("Location: ./index.php");
        die();
      }
      else $_SESSION['reg_err'] = "Please fill all the details";
    }
    else $_SESSION['reg_err'] = "Please fill all the details";
  }

/*
        $profile='http://community.topcoder.com/tc?module=MemberProfile&cr=22700151';
        parse_url($profile);
        parse_str($parts['query']);
        echo $cr;
*/

?>

<!DOCTYPE html>
<html>
<head>
	<title>Register</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body background="images.jpg">
<form id="reg_form" class="center_form" method="post" action="register.php">
<table id="reg_table">
	<tr>
    	<td>Name : </td> 
    	<td><input type="text" id="name" name="name" ></td>
	</tr>
	<br>
	<tr>
    	<td>Top-coder handle :</td> 
    	<td><input type="text" id="user_nm" name="handle"></td>
	</tr>
	<br>
	<tr>
        <td>Top-coder Profile URL:</td> 
        <td><input type="text" id="profile" name="profile"></td>
	</tr>
	<br>
	<tr>
    	<td>Password :</td>
    	<td><input type="password" id="pass" name="password"></td>
	</tr>
	<br>
	<tr>
	    <td><input type="submit" value="Register" id="register" name="submit"></td>
	</tr>-
	<br>
	<tr>
	    <td><a href="login.php"> Login Page </a></td>
	<tr>
	<tr>
	    <td><?php
	            if(isset($_SESSION['reg_err'])) echo $_SESSION['reg_err'];
	        ?>
	    </td>
	</tr>
</table>
</form>
</body>
</html>