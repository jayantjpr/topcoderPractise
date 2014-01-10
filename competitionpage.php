<?php
    include_once("generalclasses.php");
    include_once("competitionclass.php");
    include_once("problemclass.php");
    include_once("registrantclass.php");
    include_once("submissionclass.php");

    $database = new Database;

    if (isset($_GET['comp'])){
            $competition = new Competition;
            $competition -> read($database, array($_GET['comp']));
            $problems = $competition->getProblems();
    }
    else{
      die("The contest is not yet active");
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title> Contests </title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body background="images.jpg">
<div id="header">
    <div id= "tabs">
        <input type="button" value="Competition" onclick="location.href='dashboard.php'" >
        <input type="button" value="Rules" onclick="location.href='rules.php'">
    </div>

    <h1 id="site_name">Practice Website</h1>
</div>



<div id="comp">
    
        <h2><?php echo $competition->getName(); ?></h2>
        <h3> Description </h3>
        <p><?php echo $competition->getDescription(); ?></p>
        <h3> Problems </h3>
        
        <?php
            foreach($problems as $problem){
                if ($competition -> getIsEvaluated() == 't')
                    echo "<a href=\"http://community.topcoder.com/stat?c=problem_statement&pm=".$problem -> getId()."\" >";
                echo $problem->getName();
                if ($competition -> getIsEvaluated() == 't') echo "</a>";
                echo "\t".$problem->getRoomName()." (Level : ".$problem->getLevel().")\t";
                echo '<input type="button" value="I pass system test">';
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
</body>
</html>