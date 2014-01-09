<?php

  include_once("generalclasses.php");
  include_once("competitionclass.php");
  include_once("problemclass.php");
  include_once("registrantclass.php");

  var_dump(array_merge(Competition::getPrimaryFieldList(), Problem::getPrimaryFieldList()));
  $database = new Database;
  $competition = new Competition;
  $competition -> read($database,array(1));
  var_dump($competition);


  $registrant = new Registrant;
  $registrant -> read($database, array(23101510));
  var_dump($registrant);

 ?>