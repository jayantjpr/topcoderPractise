<?php

//Branchh
class Branch implements iObject, JsonSerializable{

  //Static Members Variables
  
  /** @const **/
  private static $tables;
  /** @const **/
  private static $primaryFieldList;
  /** @const **/
  private static $tablesFieldList;
  

  //Static Member Function
  
  public static function init(){
    self::$tables = array("Branch");
    self::$primaryFieldList = array("branchCode");
    self::$tablesFieldList = array(
                "branchCode",
                "branchName",
                "websiteUrl"
            );  
  }
  
  public static function getTablesName(){
    return self::$tables;
  }
  
  public static function getPrimaryFieldList(){
    return self::$primaryFieldList;
  }
  
  public static function getTablesFieldList(){
    return self::$tablesFieldList;
  }


  
  //Object Members Variables
  
  private $branchCode;
  private $branchName;
  private $websiteUrl;

  
  //Object Member functions
  
  public function getBranchCode(){
    return $this -> branchCode;
  }
  
  public function getBranchName(){
    return $this -> branchName;
  }
  
  public function getWebsiteUrl(){
    return $this -> websiteURL;
  }
  
  public function getValueArray(){
    return array($branchCode,$branchName,$websiteURL);
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

  public function read(Database $database, array $primaryKeys){
    
    //echo implode(",",self::$tables)."\n".implode(",",self::$tablesFieldList)."\n".implode(",",self::$primaryFieldList)."\n";
    
    //Form Query
    $query = Database::formSelectQuery(self::$tables, self::$tablesFieldList, self::$primaryFieldList, $primaryKeys);
          
    //Get Result
    $result =  $database -> executeQuery($query);
     if (pg_num_rows($result) < 1)
      die("No such Branch");
    $detailArray = pg_fetch_array($result, 0, PGSQL_ASSOC);

    //Fill Objects with details
    $this -> setFromSuperset($detailArray);      
  }

  public function insert(Database $database){
  
  }

  public function toJson(){
    //return json_encode(serialize($this));
    return json_encode($this);
  }

  public function jsonSerialize()
  {
    return get_object_vars($this);
  }  


  public function toHtml(){
      //echo "<table class='table table-striped table-condensed'>";
      
      echo "<tr>";
      echo "<td>Branch Code</td>";
      echo "<td>".$this->branchCode."</td>";
      echo "</tr>";
      
      echo "<tr>";
      echo "<td>Branch Name</td>";
      echo "<td>".$this->branchName."</td>";
      echo "</tr>";
      
      echo "<tr>";  
      echo "<td>Website URL</td>";
      echo "<td>".$this->websiteUrl."</td>";
      echo "</tr>";
      
      //echo "</table>";
  }
}


//Program Class
class Program implements iObject, JsonSerializable{

  //Static Members Variables

  /** @const **/
  private static $tables;
  /** @const **/
  private static $primaryFieldList;
  /** @const **/
  private static $tablesFieldList;
  
  
  //Static Member Function
  
  public static function init(){
    self::$tables = array("Program", "Branch");
    self::$primaryFieldList = array("branchCode", "programType");
    self::$tablesFieldList = array(
                "programType",
                "websiteUrl",
                "brochure",
                "placementSecRollNo",
                "facultyAdvisorName",
                "facultyAdvisorContact",
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

  public static function getAllPrograms(Database $database){

    $tablesFieldList = array_merge(self::$tablesFieldList, Branch::getTablesFieldList());
    $query = Database::formSelectQuery(self::$tables, $tablesFieldList, array(), array());
          
    //Get Result
    $result =  $database -> executeQuery($query);
    $numerOfRows = pg_num_rows($result);
    if ($numerOfRows < 1)
      die("No Programs");
    
    for ($i=0; $i < $numerOfRows; $i++) { 
      $detailArray = pg_fetch_array($result, $i, PGSQL_ASSOC);
      $programs[$i] = new Program;
      $programs[$i] -> setFromSuperset($detailArray);
    }
    //var_dump($programs); die();
    return $programs;
  }
  
 
  
  //Object Members Variables
  
  private $programType;
  private $websiteUrl;
  private $brochure;
  private $placementSecRollNo;
  private $facultyAdvisorName;
  private $facultyAdvisorContact;
  private $branch;

  
  //Object Member functions
  
  function __construct(){
    $this -> branch = new Branch;
  }

  public function getProgramType(){
    return $this -> programType;
  }

  public function getWebsiteUrl(){
    return $this -> websiteUrl;
  }

  public function getBrochure(){
    return $this -> brochure;
  }

  public function getPlacementSecRollNo(){
    return $this -> placementSecRollNo;
  }

  public function getFacultyAdvisorName(){
    return $this -> facultyAdvisorName;
  }

  public function getFacultyAdvisorContact(){
    return $this -> facultyAdvisorContact;
  }

  public function getBranch(){
    return $this -> branch;
  }

  public function getValueArray(){
    return array($programType,$websiteUrl,$brochure,$placementSecRollNo,$facultyAdvisorName,$facultyAdvisorContact);
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
    $this -> branch -> setFromSuperset($details); 
  }

  public function read(Database $database, array $primaryKeys){
    
    //echo implode(",",self::$tables)."\n".implode(",",self::$tablesFieldList)."\n".implode(",",self::$primaryFieldList)."\n";
    
    //Form Query
    $tablesFieldList = array_merge($tablesFieldList, Branch::getTablesFieldList());
    $query = Database::formSelectQuery(self::$tables, $tablesFieldList, self::$primaryFieldList, $primaryKeys);
          
    //Get Result
    $result =  $database -> executeQuery($query);
     if (pg_num_rows($result) < 1)
      die("No Student with this Roll Number");
    $detailArray = pg_fetch_array($result, 0, PGSQL_ASSOC);

    //Fill Objects with details
    $this -> setFromSuperset($detailArray);
    
  }


  public function insert(Database $database){
  }

  public function toJson(){
    //return json_encode(serialize($this));
    return json_encode($this);
  }

  public function jsonSerialize()
  {
    return get_object_vars($this);
  }  

  public function toHtml()
  {
      echo "<table class='table table-striped table-condensed'>";
      
      echo "<tr>";
      echo "<td>Program Type</td>";
      echo "<td>".$this->programType."</td>";
      echo "</tr>";
      
      echo "<tr>";
      echo "<td>Website URL</td>";
      echo "<td>".$this->websiteUrl."</td>";
      echo "</tr>";
      
      echo "<tr>";  
      echo "<td>Brochure</td>";
      echo "<td>".$this->brochure."</td>";
      echo "</tr>";
      
      echo "<tr>";  
      echo "<td>Placement Secretary Roll Number</td>";
      echo "<td>".$this->placementSecRollNo."</td>";
      echo "</tr>";
      
      echo "<tr>";  
      echo "<td>Faculty Advisor Name</td>";
      echo "<td>".$this->facultyAdvisorName."</td>";
      echo "</tr>";
        
      echo "<tr>";
      echo "<td>Faculty Advisor Contact</td>";
      echo "<td>".$this->facultyAdvisorContact."</td>";
      echo "</tr>";

      $this -> branch -> toHtml();
      
      echo "</table>";
  }


}

Branch::init();
Program::init();

?>