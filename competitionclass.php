<?php

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
  /** @const **/
  private static $solvedProblemTables;

//Static Member Functions
  public static function init(){
    self::$tables = array("Competition");
    self::$competitionProblemsTables = array("CompProb");
    self::$solvedProblemTables = array("CompProbRegistrant");
    self::$primaryFieldList = array("idc");
    self::$tablesFieldList = array
    (
      "idc",
      "name",
      "startTime",
      "endTime",
      "description",
      "resultEval"
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
  private $startTime;
  private $endTime;
  private $description;
  private $problems;
  private $resultEval;

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
    return $this -> startTime;
  }

  public function getEndTime(){
    return $this -> endTime;
  }

  public function getDescription(){
    return $this -> description;
  }

  public function getProblems(){
    return $this -> problems;
  }

  public function getResultEval(){
    return $this -> resultEval;
  }

  //Setters
  public function setId($id){
    $this -> idc = $id;
  }

  public function setName($name){
    $this -> name = $name;
  }

  public function setStartTime($startTime){
    $this->startTime = $startTime;
  }

  public function setEndTime($endTime){
    $this->endTime = $endTime;
  }

  public function setDescription($description){
    $this -> description = $description;
  }

  public function setResultEval($resultEval){
    return $this -> resultEval = resultEval;
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
    $subQuery = Database::formSelectQuery($competitionProblemsTables, array(), self::$primaryFieldList, $primaryKeys)." AS T ";
    $query = Database::formSelectQuery(array_merge(array($subQuery), Problem::getTables()), Problem::getTablesFieldList(), array(), array());
        
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
                    $this -> startTime,
                    $this -> endTime,
                    $this -> description,
                    $this -> resultEval,
             ));
    $result =  $database -> executeQuery($query);
    
    foreach ($this -> problems as $problem) {
      $problem -> insert($database);
    }
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
