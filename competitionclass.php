<?php

  include_once("generalclasses.php");
  include_once("problemclass.php");

//Competition Class
class Competition implements iObject, JsonSerializable{


//Static Attributes
  /** @const **/
  private static $tables;
  /** @const **/
  private static $primaryFieldList;
  /** @const **/
  private static $tablesFieldList;
  /** @const **/
  private static $competitionProblemsTables;

//Static Member Functions
  public static function init(){
    self::$tables = array("competitions");
    self::$competitionProblemsTables = array("competitions_problems");
    self::$primaryFieldList = array("idc");
    self::$tablesFieldList = array
    (
      "idc",
      "name",
      "start_time",
      "end_time",
      "description",
      "isevaluated"
    );
  }

  public static function getTablesName(){
    return self::$tables;
  }

  public static function getTablesFieldList(){
    return self::$tablesFieldList;
  }

  public static function getPrimaryFieldList(){
    return self::$primaryFieldList;
  }


//Private Attributes
  private $idc;
  private $name;
  private $start_time;
  private $end_time;
  private $description;
  private $problems;
  private $isevaluated;

//Functions
  function __construct(){
  }

  //Getters
  public function getId(){
    return $this -> idc;
  }

  public function getName(){
    return $this -> name;
  }

  public function getStartTime(){
    return $this -> start_time;
  }

  public function getEndTime(){
    return $this -> end_time;
  }

  public function getDescription(){
    return $this -> description;
  }

  public function getProblems(){
    return $this -> problems;
  }

  public function getResultEval(){
    return $this -> isevaluated;
  }

  //Setters
  public function setId($id){
    $this -> idc = $id;
  }

  public function setName($name){
    $this -> name = $name;
  }

  public function setStartTime($start_time){
    $this->start_time = $start_time;
  }

  public function setEndTime($end_time){
    $this->end_time = $end_time;
  }

  public function setDescription($description){
    $this -> description = $description;
  }

  public function setResultEval($isevaluated){
    return $this -> isevaluated = isevaluated;
  }

  public function setFromSubset(array $details){
    foreach ($details as $key => $value) {
      $this -> $key = $value;
    }
  }

  public function setFromSuperset(array $details){
    foreach (self::getTablesFieldList() as $key) {
      $this -> $key = $details[$key];
    }
  }

  //Database Handling Functions

  //Read Object from Database
  public function read(Database $database, array $primaryKeys){

    //Competition Details

    //Form Query
    $query = Database::formSelectQuery(self::$tables, self::$tablesFieldList, self::$primaryFieldList, $primaryKeys);
    
    //Get Result from database
    $result =  $database -> executeQuery($query);
    $numberOfRows = pg_num_rows($result); 
    if ( $numberOfRows < 1)
      return 1;//die("No Student with this Roll Number");
  
    //Fill the Objects with details
    $detailArray = pg_fetch_array($result, 0, PGSQL_ASSOC);
    $this -> setFromSubset($detailArray);


    //Problems

    //Form Query
    $subQuery = Database::formSelectQuery(self::$competitionProblemsTables, array(), self::$primaryFieldList, $primaryKeys)." AS T ";
    $query = Database::formSelectQuery(array_merge(array($subQuery), Problem::getTablesName()), Problem::getTablesFieldList(), array(), array());
        
    //Get Result from database
    $result =  $database -> executeQuery($query);
    $numberOfRows = pg_num_rows($result); 
    if ( $numberOfRows < 1)
      return 1;//die("No Student with this Roll Number");
  
    //Fill the problems array
    $detailArray = pg_fetch_array($result, 0, PGSQL_ASSOC);
    $this -> problems[0] = new Problem;
    $this -> problems[0] -> setFromSubset($detailArray);
    for ($i=1; $i < $numberOfRows; $i++) { 
      $detailArray = pg_fetch_array($result, $i, PGSQL_ASSOC);
      $this -> problems[$i] = new Problem;
      $this -> problems[$i] -> setFromSubset($detailArray);
    }
  }

  
  //Insert Object into the Database
  public function insert(Database $database){
    $query = Database::formInsertQuery(self::$tables[0] ,self::$tablesFieldList, array(
                    $this -> idc,
                    $this -> name,
                    $this -> start_time,
                    $this -> end_time,
                    $this -> description,
                    $this -> isevaluated,
             ))." RETURNING ".self::$primaryFieldList[0];
    $result =  $database -> executeQuery($query);
    
    foreach ($this -> problems as $problem) {
      $problem -> insert($database);
    }
  }

  public function updateIsEvaluated(Database $database, $value){
    //Form Query
    $query = Database::formUpdateQuery(self::$tables[0], array(self::$tablesFieldList[5]), array($value),
                                        self::$primaryFieldList,
                                        array($this -> idc)
                                      );
    //Get Result from database
    $result =  $database -> executeQuery($query);
    $numberOfRows = pg_affected_rows($result); 
    if ($numberOfRows < 1)
      return 1;//die("No such submission");
  }

  public function update(Database $database){

  }


  //Object Representation Functions
  public function toJson(){
    return json_encode($this, JSON_PRETTY_PRINT);
  }

  public function jsonSerialize(){
    return get_object_vars($this);
  }  
}

Competition::init();

?>
