<?php

//Competition Class
class Problem implements iObject, JsonSerializable{


//Static Attributes
  /** @const **/
  private static $tables;
  /** @const **/
  private static $primaryFieldList;
  /** @const **/
  private static $tablesFieldList;


//Static Member Functions
  public static function init(){
    self::$tables = array("Problem");
    self::$primaryFieldList = array("idp");
    self::$tablesFieldList = array
    (
      "idp",
      "name",
      "roomId",
      "roomName",
      "level"
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
  private $roomId;
  private $roomName;
  private $level;
  

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
    return $this -> roomId;
  }

  public function getRoomName(){
    return $this -> roomName;
  }

  public function getLevel(){
    return $this -> level;
  }

  //Setters
  public function setId($id){
    $this -> idp = $id;
  }

  public function setName($name){
    $this -> name = $name;
  }

  public function setRoomId($roomId){
    $this -> roomId = $roomId;
  }

  public function setRoomName($roomName){
    $this -> roomName = $roomName;
  }

  public function setLevel($level){
    $this -> level = $level;
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
                    $this -> roomId,
                    $this -> roomName,
                    $this -> level
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