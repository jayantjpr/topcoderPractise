<?php


//Submission Class
class Submission implements iObject{

//Static Attributes
  /** @const **/
  private static $tables;
  /** @const **/
  private static $primaryFieldList;
  /** @const **/
  private static $tablesFieldList;


//Static Member Functions
  public static function init(){
    self::$tables = array("submissions");
    self::$primaryFieldList = array("idc", "idp", "idr");
    self::$tablesFieldList = array
    (
      "idc",
      "idp",
      "idr",
      "score"
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
  private $idp;
  private $idr;
  private $score;
  private $count;
  private $registrant;  //object


//Functions
  function __construct(){
    $this -> registrant = new Registrant;
    $this -> score = "0";
    $this -> count = "1";
  }

  //Getters
  public function getCompetitionId(){
    return $this -> idc;
  }

  public function getProblemId(){
    return $this -> idp;
  }

  public function getRegistrantId(){
    return $this -> idr;
  }

  public function getScore(){
    return $this -> score;
  }

  public function getCount(){
    return $this -> count;
  }

  public function getRegistrant(){
    return $this -> registrant;
  }

  
  //Setters
  public function setCompetitionId($idc){
    $this -> idc = $idc;
  }

  public function setProblemId($idp){
    $this -> idp = $idp;
  }

  public function setRegistrantId($idr){
    $this -> idr = $idr;
  }
  
  public function setScore($score){
    $this -> score = $score;
  }

  public function setRegistant($detailArray){
    $this -> registrant -> setFromSuperset($detailArray);
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
      return -1;//die("No Student with this Roll Number");
  
    //Fill the Objects with details
    $detailArray = pg_fetch_array($result, 0, PGSQL_ASSOC);
    $this -> setFromSubset($detailArray);
  }


  public static function getSubmissionsFor($database, $idc, $idp){
    //Form Query
    $query = Database::formSelectQuery(self::$tables, self::$tablesFieldList, array_slice(self::$primaryFieldList,0,2), array($idc, $idp));
    
    //Get Result from database
    $result =  $database -> executeQuery($query);
    $numberOfRows = pg_num_rows($result); 
    if ( $numberOfRows < 1)
      return -1;//die("No Student with this Roll Number");
  
    //Fill the registrant array
    $detailArray = pg_fetch_array($result, 0, PGSQL_ASSOC);
    $submissions[0] = new Submission;
    $submissions[0] -> setFromSubset($detailArray);
    for ($i=1; $i < $numberOfRows; $i++) { 
      $detailArray = pg_fetch_array($result, $i, PGSQL_ASSOC);
      $submissions[$i] = new Submission;
      $submissions[$i] -> setFromSubset($detailArray);
    }
    return $submissions;
  }

  public static function getLeadboardFor($database, $idc){
    //Form Query
    $sub_query = "(SELECT ".self::$tablesFieldList[2].", SUM(score) AS ".self::$tablesFieldList[3].", COUNT(*) AS count".
      " FROM ".self::$tables[0]." WHERE ".self::$tablesFieldList[0]." = ".$idc." GROUP BY ". self::$tablesFieldList[2].") AS T";
    $query = Database::formSelectQuery(array_merge(array($sub_query), Registrant::getTablesName()), array(), array(), array())." ORDER BY count, ". self::$tablesFieldList[3]." DESC";
    //var_dump($query);

    //Get Result from database
    $result =  $database -> executeQuery($query);
    $numberOfRows = pg_num_rows($result); 
    if ( $numberOfRows < 1)
      return -1;//die("No Student with this Roll Number");
  
    //Fill the registrant array
    $detailArray = pg_fetch_array($result, 0, PGSQL_ASSOC);
    $submissions[0] = new Submission;
    $submissions[0] -> setFromSubset($detailArray);
    $submissions[0] -> setRegistant($detailArray);
    for ($i=1; $i < $numberOfRows; $i++) { 
      $detailArray = pg_fetch_array($result, $i, PGSQL_ASSOC);
      $submissions[$i] = new Submission;
      $submissions[$i] -> setFromSubset($detailArray);
      $submissions[$i] -> setRegistant($detailArray);
    }
    return $submissions;
  }


  
  //Insert Object into the Database
  public function insert(Database $database){
    $query = Database::formInsertQuery(self::$tables[0] ,self::$tablesFieldList, array(
                    $this -> idc,
                    $this -> idp,
                    $this -> idr,
                    $this -> score
             ));
    $result =  $database -> executeQuery($query);
    $numberOfRows = pg_affected_rows($result); 
    if ($numberOfRows < 1)
      return -1;//die("Insert Unsuccessful");
  }


  //Update Database
  public function updateScore($database){
    //Form Query
    $query = Database::formUpdateQuery(self::$tables[0], array(self::$tablesFieldList[3]), array($this -> score), 
                                        self::$primaryFieldList,
                                        array($this -> idc, $this -> idp, $this -> idr)
                                      );
    //Get Result from database
    $result =  $database -> executeQuery($query);
    $numberOfRows = pg_affected_rows($result); 
    if ($numberOfRows < 1)
      return -1;//die("Update Unsuccessful");
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

Submission::init();

?>