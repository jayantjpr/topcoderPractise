<?php
    include_once("generalclasses.php");
    include_once("competitionclass.php");
    include_once("problemclass.php");
    include_once("registrantclass.php");
    include_once("submissionclass.php");

    $database = new Database;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
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

<h3>Competitions</h3>
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
                            echo "<a href=\".\competitionpage.php?comp=".$competition->getId()."\">".$competition -> getName()."</a>";
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