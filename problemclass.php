<?php

include_once("generalclasses.php");
include_once("competitionclass.php");

//Competition Class
class Problem implements iObject{


//Static Attributes
  /** @const **/
  private static $tables;
  /** @const **/
  private static $primaryFieldList;
  /** @const **/
  private static $tablesFieldList;


//Static Member Functions
  public static function init(){
    self::$tables = array("problems");
    self::$primaryFieldList = array("idp");
    self::$tablesFieldList = array
    (
      "idp",
      "name",
      "room_id",
      "room_name",
      "difficulty_level"
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
  private $idp;
  private $name;
  private $room_id;
  private $room_name;
  private $difficulty_level;
  

//Functions
  function __construct(){
  }

  //Getters
  public function getId(){
    return $this -> idp;
  }

  public function getName(){
    return $this -> name;
  }

  public function getRoomId(){
    return $this -> room_id;
  }

  public function getRoomName(){
    return $this -> room_name;
  }

  public function getLevel(){
    return $this -> difficulty_level;
  }

  //Setters
  public function setId($id){
    $this -> idp = $id;
  }

  public function setName($name){
    $this -> name = $name;
  }

  public function setRoomId($room_id){
    $this -> room_id = $room_id;
  }

  public function setRoomName($room_name){
    $this -> room_name = $room_name;
  }

  public function setLevel($difficulty_level){
    $this -> difficulty_level = $difficulty_level;
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
  }

  
  //Insert Object into the Database
  public function insert(Database $database){
    $query = Database::formInsertQuery(self::$tables[0] ,self::$tablesFieldList, array(
                    $this -> idp,
                    $this -> name,
                    $this -> room_id,
                    $this -> room_name,
                    $this -> difficulty_level
             ));
    $result =  $database -> executeQuery($query);
  }

  public function update(Database $database){

  }


  //Object Representation Functions
  public function toJson(){
    return json_encode($this, JSON_PRETTY_PRINT);
  }

  public function jsonSerialize()
  {
    return get_object_vars($this);
  }  
}

Problem::init();
?>