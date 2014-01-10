<?php

  include_once("generalclasses.php");
  include_once("competitionclass.php");
  include_once("problemclass.php");
  include_once("registrantclass.php");
  include_once("submissionclass.php");

  $auth = base64_encode('jayant:4Rjk47nx');
  $aContext = array(
    'http' => array(
        'proxy' => 'tcp://202.141.80.22:3128',
        'request_fulluri' => true,
        'header' => "Proxy-Authorization: Basic $auth",
    ),
  );
  $cxContext = stream_context_create($aContext);

  $database = new Database;

  var_dump($_SERVER['argv']);

  if (isset($_SERVER['argv'][1])){

    $competition = new Competition;
    $competition -> read($database,array($_SERVER['argv'][1]));
    
    $problems = $competition -> getProblems();
    foreach ($problems as $problem){
      //submissions for this problem
      $registrants = Submission::getSubmissionsFor($database, $competition -> getId(), $problem -> getId());
      
      //get topcoder details of the room
      $sFile = file_get_contents("http://www.topcoder.com/tc?module=BasicData&c=dd_algo_practice_room_detail&dsid=30&rd=".$problem -> getRoomId(), False, $cxContext);
      $xml = simplexml_load_string($sFile);
      
      //put score of all registrants
      foreach ($registrants as $registrant) {
        //search XML for the registrant for this problem
        foreach ($xml->row as $row)
          if ($row -> coder_id == $registrant -> getRegistrantId() && $row -> problem_id == $problem -> getId()){
            //get score
            $score = $row -> points;
            //update his score
            $registrant -> setScore($score);
            $registrant -> updateScore($database);
            break;
          } 
      }
    }
    $competition -> setIsEvaluated(true);
    $competition -> updateIsEvaluated($database);
  
  }
  die();  
 ?>