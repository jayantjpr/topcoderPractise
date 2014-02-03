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

    if (isset($_GET['comp'])){
            $competition = new Competition;
            $result = $competition -> read($database, array($_GET['comp']));
            if ($result == -1){
                header("Location: ./index.php");
                die("No such competition");
            }

            $tz = new DateTimeZone('Asia/Calcutta');
            $curtime = new DateTime(NULL, $tz);
            $compTime = new DateTime($competition -> getStartTime(), $tz);
            $diff = $compTime -> diff($curtime);
            if ($diff -> format('%R') == "+"){
                $problems = $competition->getProblems();
            }
            else
                die("The contest is not yet active");;
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title> Contests </title>
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


    <div id="comp">
        <h2><?php echo $competition->getName(); ?></h2>
        <br/>
        <h3> Description </h3>
        <p><?php echo $competition->getDescription(); ?></p>
        <br/>
        <h3> Problems </h3>
        
        <?php
            foreach($problems as $problem){
                if ($competition -> getIsEvaluated() == 't')
                    echo "<a href=\"http://community.topcoder.com/stat?c=problem_statement&pm=".$problem -> getId()."\" >";
                echo $problem->getName();
                if ($competition -> getIsEvaluated() == 't') echo "</a>";
                echo "\t".$problem->getRoomName()." (Level : ".$problem->getLevel().")\t";
                if ($competition -> getIsEvaluated() != 't') 
                    echo '<input type="button"  name = "'.$competition -> getId().','.$problem -> getId().','.$_SESSION['idr'].
                    '" value="I pass system test" class = "passed">';
                echo "<br>";
            }
        ?>
    </div>
    <br>
    <?php
        if ($competition -> getIsEvaluated() == 't'){
            echo '<h3>Leader Board</h3>
                    <div id="leader_board "class="CSSTableGenerator" >
                        <table cellspacing="0">
                            <tr>
                                <td> Rank </td>
                                <td> Name </td>
                                <td> Score </td>
                            </tr>';
            
                            $registrants = Submission::getLeadboardFor($database, $competition -> getId());
                            $len = sizeof($registrants);
                            for ($i=0; $i < $len; $i++){
                                echo "<tr>
                                        <td>".($i+1)."</td>";
                                echo "  <td><a href=\"http://community.topcoder.com/tc?module=MemberProfile&cr=".$registrants[$i] -> getRegistrant() -> getId()."\">".
                                                $registrants[$i] -> getRegistrant() -> getName()." (".$registrants[$i] -> getRegistrant() -> getHandle().")
                                            </a>
                                        </td>";
                                echo "  <td>".$registrants[$i] -> getScore()."</td>";
                                echo "</tr>";
                            }
            echo '      </table>
                    </div>';

        }
    ?>
    <script src="./jquery.js"></script>
    <script>
        $.ajaxSetup ({  
            cache: false  
        });

        var loadUrl = "./function.php";

        $(".passed").click(function(){
          var sub = this.name.split(',');
          $.post(
                loadUrl,
                {idc: sub[0], idp: sub[1], idr: sub[2]},
                function(responseText){  
                    alert(responseText);  
                },
                "text"
            );  
        });
    </script>
</body>
</html>