<?php
	// This starts the session which is like a cookie, but it isn't saved on your hdd and is much more secure.
	session_start(); 

	// That bit of code checks if you are logged in or not, and if you are, you can't log in again!
	if(isset($_SESSION['idr'])){
    if ($_SESSION['idr'] == "0"){
      echo "God! You are the admin. Redirecting you to contest setting form ...";
      header("Location: ./admin.php");
    }
    else{
      echo "You are already logged in! Redirecting you to dashboard ...";
      header("Location: ./dashboard.php");
    }
	}
  else {
    echo "You are not logged in! Redirecting you to signin ...";
    header("Location: ./login.php");
  }
  die();

?>