<?php
    //This starts the session which is like a cookie, but it isn't saved on your hdd and is much more secure.
    session_start();

    // That bit of code checks if you are logged in or not, and if you are, you can't log in again!
    if(!isset($_SESSION['idr']) || $_SESSION['idr'] != "0"){
        echo "You are not admin! Redirecting you to correct page ...";
        header("Location: ./index.php");
        die();
    }

  include_once("generalclasses.php");
  include_once("competitionclass.php");
  include_once("problemclass.php");
          
  if(isset($_POST['submit'])){
    $comp_name = trim($_POST['comp_name']);
    $startdate = trim($_POST['startdate']);
    $starttime = trim($_POST['starttime']);
    $enddate = trim($_POST['enddate']);
    $endtime = trim($_POST['endtime']);
    $endtime = trim($_POST['endtime']);
    $description = trim($_POST['description']);
    for ($i=1; $i <= 5; $i++) {
        $room = trim($_POST["room".$i]);
        $name = trim($_POST["name".$i]);
        $idp = trim($_POST["idp".$i]);
        $level = trim($_POST["level".$i]);
        if (!empty($room) && !empty($idp) && !empty($name) && !empty($level)){
            $problem = new Problem;
            $room = explode(",", $room);
            $problem -> setRoomId($room[0]);
            $problem -> setRoomName($room[1]);
            $problem -> setName($name);
            $problem -> setLevel($level);
            $problem -> setId($idp);
            $problems[] = $problem;
        }
    }
    
    if (!empty($comp_name) && !empty($startdate) && !empty($starttime) && !empty($enddate) && !empty($endtime) && !empty($description) && !empty($problems)){
        $start_time = $startdate." ".$starttime;
        $end_time = $enddate." ".$endtime;
        $competition = new Competition;
        $competition -> setName($comp_name);
        $competition -> setStartTime($start_time);
        $competition -> setEndTime($end_time);
        $competition -> setDescription($description);
        $competition -> setProblems($problems);

        //add competition to database
        $dbh = new Database;
        if (!$dbh){
          die("Error in connection: " . pg_last_error());
        }
        $competition -> insert($dbh);
    }
    else $_SESSION['err'] = "Please fill all the details.<br\> There must be atleast one competition with complete details. All other details must be filled.";
  }
?>

<!DOCTYPE html>
<html>
<head>
	<title>Admin_page</title>
	<link rel="stylesheet" type="text/css" href="./style.css">
	<meta charset="utf-8">
    <link rel="stylesheet" href="ui.css">
    <script src="./jquery.js"></script>
    <script src="./jquery1.js"></script>
    <script>
        $(function() {
            $( "#datepicker1" ).datepicker({ 'dateFormat': 'yy/mm/dd' });
            $( "#datepicker2" ).datepicker({ 'dateFormat': 'yy/mm/dd' });
            $( "#timepicker1").timepicker({ 'timeFormat': 'H:i:s' });
            $( "#timepicker2").timepicker({ 'timeFormat': 'H:i:s' });
        });
    </script>
</head>


<body background="./images.jpg">

<div id="page-wrap">

	<h1 align=center>Welcome to admin page</h1>

	<ul class="breadcrumb">
		<li><a href="./dashboard.php">Competitions</a></li>
		<li><a href="./rules.php">Rules</a></li>
        <li><a href="logout.php">Log Out</a></li>
		<li></li>
	</ul>

</div>
	<!--div>
        <p>Date: <input type="text" id="datepicker"></p>
    </div-->
<div id="form_div" style="padding-left: 40%">
    <form id="competetion" class="admin_select" method="post" action="admin.php">
        Competetion Name: <input type="text" name="comp_name" placeholder="eg. Dymanic Programming 1">
        <br><br>
        
        
        <p>Start Date</p>
        Date: <input type="text" id="datepicker1" name="startdate" placeholder="yyyy/mm/dd">
        time: <input type="text" id="timepicker1" name="starttime" placeholder="hh:mm:ss">
        <br>
        
        <p>End Date</p>
        Date: <input type="text" id="datepicker2" name="enddate" placeholder="yyyy/mm/dd">
        time: <input type="text" id="timepicker2" name="endtime" placeholder="hh:mm:ss">
        <br><br>
        
        
        <textarea rows="4" cols="50" name="description" placeholder="Description here !!!">    
        </textarea>
        <br><br>
        
        <?php
            $auth = base64_encode('proxy-username:proxy-password');
            $aContext = array(
                'http' => array(
                    'proxy' => 'tcp://202.141.80.22:3128',
                    'request_fulluri' => true,
                    'header' => "Proxy-Authorization: Basic $auth",
                ),
            );
            $cxContext = stream_context_create($aContext);
            
            $sFile = file_get_contents("http://community.topcoder.com/tc?module=BasicData&c=dd_algo_practice_rooms&dsid=30", False, $cxContext);
            $xml = simplexml_load_string($sFile);

            //form options string
            $options = "";
            foreach ($xml->row as $row)
                $options = $options.'<option value="'.$row -> round_id.','.$row -> room_name.'">'.$row -> room_name."</option>\n";

            //HTML for dropdown
            echo "<p>Problems</p>\n";
            for ($i=1; $i <= 5; $i++) { 
                echo $i.".\t";
                echo'<select name="room'.$i.'">'.$options.
                    '</select>'.
                    '<select name="level'.$i.'">
                        <option value="1">200</option>
                        <option value="2">500</option>
                        <option value="3">1000</option>
                    </select>'.
                    '<input type="text" id="ques_id" name="idp'.$i.'" placeholder="Question ID(from URL end)">
                    <input type="text" id="ques_name" name="name'.$i.'" placeholder="Question Name(Class Name)">
                    <br>';
            }
        ?>

        <br/>
        <?php
                if(isset($_SESSION['err'])) echo $_SESSION['err']."<br/>";
        ?>
        <br/>      
        <input type="submit" value="Submit" name = "submit">
    </form>
</div>
</body>
</html>