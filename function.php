<?php

    // This starts the session which is like a cookie, but it isn't saved on your hdd and is much more secure.
    session_start();

    // That bit of code checks if you are logged in or not, and if you are, you can't log in again!
    if(!isset($_SESSION['idr'])){
        echo "You are already logged in! Redirecting you to correct page ...";
        header("Location: ./index.php");
        die();
    }

    include_once("generalclasses.php");
    include_once("competitionclass.php");
    include_once("problemclass.php");
    include_once("registrantclass.php");
    include_once("submissionclass.php");

    $database = new Database;

    if(isset($_POST['idc']) && isset($_POST['idp']) && isset($_POST['idr'])){
        $submission = new Submission;
        $submission -> setFromSubset($_POST);
        $result = $submission -> insert($database);
        if ($result == -1) echo "Some problem with submission";
        else echo "Solution recorded";
    }
?>