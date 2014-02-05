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
    if (!$database){
        die("Error in connection: " . pg_last_error());
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body background="images.jpg">
    <div id="page-wrap">
    
        <h1>Practise website</h1>

        <ul class="breadcrumb">
            <li><a href="dashboard.php">Competeitions</a></li>
            <li><a href="rules.php">Rules</a></li>
            <li><a href="logout.php">Log Out</a></li>
            <li></li>
        </ul>
    
    </div>


<h2 style="text-align:center"> Competitions </h2>
<div id="comp_list "class="CSSTableGenerator" >
    <table  cellspacing="0">
        <tr>
            <td>Name</td>
            <td>Start Time</td>
            <td>End Time</td>
        <tr>
        <?php
            $competitions = Competition::readAll($database);
            $tz = new DateTimeZone('Asia/Calcutta');
            $curtime = new DateTime(NULL, $tz);
            foreach ($competitions as $competition) {
                echo "<tr>
                        <td>";
                        $compTime = new DateTime($competition -> getStartTime(), $tz);
                        $diff = $compTime -> diff($curtime);
                        if ($diff -> format('%R') == "+")
                            echo "<a href=\"./competitionpage.php?comp=".$competition->getId()."\">".$competition -> getName()."</a>";
                        else
                            echo $competition -> getName();
                echo "  </td>";
                echo "  <td>".$competition -> getStartTime()."</td>";
                echo "  <td>".$competition -> getEndTime()."</td>";
                echo "</tr>";
            }
        ?>
    </table>
</div>

</body>
</html>