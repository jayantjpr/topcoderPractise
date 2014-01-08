<?php

  //Database Access Class
  class Database  
  {  
    //Non-Static Member Variabless

    private $host;
    private $dbname;
    private $user;
    private $dbh;

    
    //Non-Static Member Functions

    function __construct($host = "localhost", $dbname = "test", $user = "postgres"){
      $this -> host = $host;
      $this -> dbname = $dbname;
      $this -> user = $user;
      $this -> dbh = NULL;

      self::connect();
    }

    function __destruct(){
      self::disconnect();
    }

    public function connect(){
      $this -> dbh = pg_connect("host=".$this -> host." dbname=".$this -> dbname." user=".$this -> user);
      if (!$this -> dbh)
        die("Error in connection: " . pg_last_error());
    }

    public function disconnect(){
      if (!$this -> dbh)
        die("Error in disconnecting:  " . pg_last_error());
      pg_close($this -> dbh);
    }

    public function executeQuery($query){
      //Fire Query to Database
      $result = pg_query($this -> dbh, $query) or die("Cannot execute query: $query\n");
      return $result;
    }
    


    //Static Member Functions

    public static function formSelectQuery(array $table, array $displayFieldList, array $whereFieldList, array $whereValues){
      //Check input correctness
      assert(count($whereFieldList) == count($whereValues), "unequal number of fields and values");
      
      //Select Query Formation
      $len = count($whereFieldList);
      
      if (empty($displayFieldList) || $displayFieldList[0] == "*")
        $query = "( SELECT * FROM ".implode(" NATURAL JOIN ", $table)." WHERE ";
      else
        $query = "( SELECT \"".implode("\",\"", $displayFieldList)."\" FROM ".implode(" NATURAL JOIN ", $table)." WHERE ";

      //echo "Got it : ".$query."\n";

      if (!empty($whereFieldList)){
        for ($i=0; $i < $len - 1; $i++){
          $query = $query."\"".$whereFieldList[$i]."\" = '".$whereValues[$i]."' AND \"";
        }
        $query = $query."\"".$whereFieldList[$len-1]."\" = '".$whereValues[$len-1]."' )";
      }
      else{
        $query = $query."true )"; 
      }

      return $query;
    }

    public static function formSpecialSelectQuery(array $table, array $displayFieldList, $whereclause){
      //Select Query Formation
      if (empty($displayFieldList) || $displayFieldList[0] == "*")
        $query = "( SELECT * FROM ".implode(" NATURAL JOIN ", $table)." WHERE ".$whereclause." )";
      else
        $query = "( SELECT \"".implode("\",\"", $displayFieldList)."\" FROM ".implode(" NATURAL JOIN ", $table)." WHERE ".$whereclause." )";

      return $query;
    }  
    
    public static function formInsertQuery($table,array $insertFieldList = NULL, array $insertValues){
      
      //Check input correctness
      assert(count($insertFieldList) == count($insertValues), "unequal number of fields and values");

      //Remove Null fields
      $result = array_filter($insertValues, "strlen");
      $insertValues = $result;
      $keys = array_keys($result);
   
      $result = array();
      foreach($keys as $i) {
          array_push($result, $insertFieldList[$i]);
      }
      $insertFieldList = $result;

      //Insert Query Formation
      $query = "INSERT INTO ".$table." ( \"".implode("\",\"", $insertFieldList)."\" ) VALUES ( '".implode("','", $insertValues)."' ) ";
      return $query;
    }  
    
    public static function formDeleteQuery(array $table, array $whereFieldList, array $whereValues){
      //Check input correctness
      assert(count($whereFieldList) == count($whereValues), "unequal number of fields and values");

      //Delet Query Formation
      $len = count($whereFieldList);
      
      $query = "DELETE FROM $table WHERE \"";
      for ($i=0; $i < $len - 1; $i++){
        $query = $query.$whereFieldList[$i]."\" = '".$whereValues[$i]."' AND \"";
      }
      $query = $query.$whereFieldList[$len-1]."\" = '".$whereValues[$len-1]."' ;";

      return $query;
    }
    
    public static function formUpdateQuery($table, array $updateFieldList, array $updateValues, array $whereFieldList, array $whereValues){
      //Check input correctness
      assert(count($whereFieldList) == count($whereValues), "unequal number of fields and values");
      assert(count($updateFieldList) == count($updateValues), "unequal number of fields and values to update");

      //Update Query Formation
      $lenUpdate = count($updateFieldList);
      $lenWhere = count($whereFieldList);
      
      $query = "UPDATE $table SET \"";
      for ($i=0; $i < $lenUpdate-1; $i++){
        $query = $query.$updateFieldList[$i]."\" = '".$updateValues[$i]."' , \"";
      }
      $query = $query.$updateFieldList[$lenUpdate-1]."\" = '".$updateValues[$lenUpdate-1]."' WHERE \"";
      if (!empty($whereFieldList)){
        for ($i=0; $i < $lenWhere - 1; $i++){
          $query = $query.$whereFieldList[$i]."\" = '".$whereValues[$i]."' AND \"";
        }
        $query = $query.$whereFieldList[$lenWhere-1]."\" = '".$whereValues[$lenWhere-1]."' ;";
      }
      else{
        $query = $query."1\";";
      }

      return $query;
    }
  
    public static function getContacts(Database $database, $table, array $whereFieldList, array $whereValues){
      $query = Database::formSelectQuery(array($table), array("telephone"), $whereFieldList, $whereValues); 
      $result =  $database -> executeQuery($query);
      
      $contacts = array();
      while ($detailArray = pg_fetch_row($result)){
        array_push($contacts, $detailArray[0]);
      }
      
      return $contacts;
    }

    public static function setContacts(Database $database, $table, $primaryKey, $primaryKeyValue, $contacts){
      foreach ($contacts as $contact) {
        $query = Database::formInsertQuery($table ,array($primaryKey,"telephone"), array($primaryKeyValue,$contact));
        $result =  $database -> executeQuery($query);
      }
    }
}


  //Interface for All Database Objects
  interface iObject
  {
    public function setFromSubset(array $details);
    public function setFromSuperset(array $details);
    public function read(Database $database, array $primaryKeys);
    //public function insert(Database $database);
    //public function update(Database $database, array $wher);
  }
  
?>  