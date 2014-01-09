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

<h3>Competition List </h3>
<div id="comp_list "class="CSSTableGenerator" >
    <table  cellspacing="0">
        <?php include_once "competition_list.php";?>
    </table>
</div>


</body>
</html>