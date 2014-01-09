<?php
    include_once("generalclasses.php");
    include_once("competitionclass.php");
    include_once("problemclass.php");
    include_once("registrantclass.php");

    $database = new Database;

    //if (isset($_GET['comp'])){
            $competition = new Competition;
            $competition -> read($database,array(1));//$_GET['comp']));
            $problems = $competition->getProblems();
    //}
?>
<!DOCTYPE html>
<html>
<head>
    <title> On going Contest </title>
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
            foreach($problems as $problem)
            {
                echo '<a href="">'.$problem->getName()."</a>";
                echo "\t".$problem->getRoomName()." (Level : ".$problem->getLevel().")\t";
                echo '<input type="button" value="I pass system test">';
                echo "<br>";
            }
        ?>
        
        
</div>
<br>
<h3>Leader Board</h3>
<div id="leader_board "class="CSSTableGenerator" >
    <table  cellspacing="0">
        <tr>
            <td> Rank </td>
            <td> Top Coder Handle </td>
            <td> Score </td>
        </tr>
        <?php
            /*
            echo "<tr>
                    <td>". Rank ."</td>
                    <td>". Top Coder Handle ."</td>
                    <td>". Score ."</td>
                </tr>";
            */
        ?>
    </table>
</div>
            
</body>
</html>