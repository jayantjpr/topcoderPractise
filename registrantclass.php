<?php


//Student Class
class Student implements iObject, JsonSerializable{


//Static Attributes
  /** @const **/
  private static $tables;
  /** @const **/
  private static $primaryFieldList;
  /** @const **/
  private static $tablesFieldList;


//Static Member Functions
  public static function init(){
    self::$tables = array("Student");
    self::$primaryFieldList = array("rollNo");
    self::$tablesFieldList = array
    (
      "rollNo",
      "name",
      "dob",
      "gender",
      "instituteEmail",
      "otherEmail",
      "category",
      "jeeGateAir",
      "curAddrRoomNo",
      "curAddrHostel",
      "dreamCompany",
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
  private $rollNo;
  private $name;
  private $dob;
  private $gender;
  private $instituteEmail;
  private $otherEmail;
  private $category;
  private $jeeGateAir;
  private $curAddrRoomNo;
  private $curAddrHostel;
  private $curAddrHostelName;
  private $dreamCompany;
  private $permanentAddress;
  private $programType;
  private $branchCode;
  private $program;
  private $academicDetails;
  private $spi;
  private $contacts;

//Functions
  function __construct(){
    $this -> permanentAddress = new PermanentAddress;
    $this -> program = new Program;
    $this -> spi = new SPI;
  }

  //Getters
  public function getRollNo(){
    return $this -> rollNo;
  }

  public function getName(){
    return $this -> name;
  }

  public function getDob(){
    return $this -> dob;
  }

  public function getInstituteEmail(){
    return $this -> instituteEmail;
  }

  public function getOtherEmail(){
    return $this -> otherEmail;
  }

  public function getCategory(){
    return $this -> category;
  }

  public function getJeeGateAir(){
    return $this -> jeeGateAir;
  }

  public function getRoomNo(){
    return $this -> curAddrRoomNo;
  }

  public function getHostel(){
    return $this -> curAddrHostel;
  }

  public function getHostelName(){
    return $this -> curAddrHostelName;
  }

  public function getDreamCompany(){
    return $this -> dreamCompany;
  }

  public function getAcademicDetails(){
    return $this -> academicDetails;
  }

  public function getProgram(){
    return $this -> program;
  }

  public function getSPI(){
    return $this -> spi;
  }

  public function getContacts(){
    return $this -> contacts;
  }

  //Setters

  public function setRollNo($rollNo){
    $this -> rollNo = $rollNo;
  }

  public function setAcademicDetails(array $details){
    $numberOfRows = count($details["_level"]);
    $count = 0;
    for ($j=0; $j < $numberOfRows; $j++) { 
      $flag = true;
      
      foreach (AcademicDetails::getTablesFieldList() as $field) {
        $arr["$field"] = $details["$field"][$j];
        if (is_null($arr["$field"])){
          $flag = false;
          break;
        }
      }
      if ($flag){
        $this -> academicDetails[$count] = new AcademicDetails;
        $this -> academicDetails[$count++] -> setFromSuperset($arr);
      }
    }
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
    $this -> curAddrHostelName = $details["curAddrHostelName"];
    $this -> programType  = $details["programType"];
    $this -> branchCode  = $details["branchCode"];  
    $this -> contacts = $details['telephone'];
    $this -> permanentAddress -> setFromSuperset($details);
    $this -> program -> setFromSuperset($details);
  }

  //Database Handling Functions

  //Read Object from Database
  public function read(Database $database, array $primaryKeys){

    //Form Query
    $tablesFieldList = array_merge(self::$tablesFieldList,
     PermanentAddress::getTablesFieldList(),
     AcademicDetails::getTablesFieldList(),
     Program::getTablesFieldList(),
     Branch::getTablesFieldList(),
     array("curAddrHostelName")
     );

    $location = "( SELECT \"locationId\" AS \"curAddrHostel\",\"locationName\" AS \"curAddrHostelName\" FROM InstituteLocation ) AS W";
    $subQuery = Database::formSelectQuery(array_merge(array($location), self::$tables) , array(), self::$primaryFieldList, $primaryKeys)." AS T ";
    $query = Database::formSelectQuery(array_merge(array($subQuery), Program::getTablesName(), AcademicDetails::getTablesName()),
                                         $tablesFieldList, array(), array());

    //Get Result from database
    $result =  $database -> executeQuery($query);
    $numberOfRows = pg_num_rows($result); 
    if ( $numberOfRows < 1)
      return 1;//die("No Student with this Roll Number");
  
    //Fill the Objects with details
    //--Academic Details--
    $detailArray = pg_fetch_array($result, 0, PGSQL_ASSOC);
    $this -> academicDetails[0] = new AcademicDetails;
    $this -> academicDetails[0] -> setFromSuperset($detailArray);
    for ($i=1; $i < $numberOfRows; $i++) { 
      $detailArray = pg_fetch_array($result, $i, PGSQL_ASSOC);
      $this -> academicDetails[$i] = new AcademicDetails;
      $this -> academicDetails[$i] -> setFromSuperset($detailArray);
    }

    //--Properties--
    $this -> setFromSuperset($detailArray);
    $this -> spi -> read($database, array($this -> program -> getProgramType(), $primaryKeys[0]));
    $this -> contacts = Database::getContacts($database, "StudentContact", self::$primaryFieldList, $primaryKeys);
  }


  /* Positions Applied for:
   * These are not stored in the oobject but are always retrieved from the database.
   */
  public function getApplyPositions(Database $database){

    if (!isset($this-> rollNo))
      return;

    //Form Query
    $tablesFieldList = array_merge(Apply::getTablesFieldList(),Position::getTablesFieldList(), Company::getTablesFieldList());

    $subQuery = Database::formSelectQuery(Apply::getTablesName(), array(), self::$primaryFieldList, array($this -> rollNo))." AS T ";
    $query = Database::formSelectQuery(array_merge(array($subQuery), Position::getTablesName(), Company::getTablesName()),
                                         $tablesFieldList, array(), array());

    //Get Result
    $result =  $database -> executeQuery($query);
    $numberOfRows = pg_num_rows($result); 
    if ( $numberOfRows < 1)
        die("No positions in the database");
      
    //Fill Objects with details
    $detailArray = pg_fetch_array($result, 0, PGSQL_ASSOC);
    $applyPositions[0] = new Apply;
    $applyPositions[0] -> setFromSuperset($detailArray);
    $applyPositions[0] -> setPosition($database, $detailArray);

    for ($i=1; $i < $numberOfRows; $i++) { 
          $detailArray = pg_fetch_array($result, $i, PGSQL_ASSOC);
          $applyPositions[$i] = new Apply;
          $applyPositions[$i] -> setFromSuperset($detailArray);
          $applyPositions[$i] -> setPosition($database, $detailArray);
    }

    return $applyPositions;
  }

  //Insert Object into the Database
  public function insert(Database $database){
    $query = Database::formInsertQuery(self::$tables[0] ,array_merge(self::$tablesFieldList, PermanentAddress::getTablesFieldList(), array("programType", "branchCode")), array(
                    $this -> rollNo,
                    $this -> name,
                    $this -> dob,
                    $this -> gender,
                    $this -> instituteEmail,
                    $this -> otherEmail,
                    $this -> category,
                    $this -> jeeGateAir,
                    $this -> curAddrRoomNo,
                    $this -> curAddrHostel,
                    $this -> dreamCompany,
                    $this -> permanentAddress -> getHouseNo(),
                    $this -> permanentAddress -> getStreet(),
                    $this -> permanentAddress -> getCity(),
                    $this -> permanentAddress -> getState(),
                    $this -> permanentAddress -> getCountry(),
                    $this -> permanentAddress -> getPincode(),
                    $this -> programType,
                    $this -> branchCode

             ));
    $result =  $database -> executeQuery($query);
    
    foreach ($this -> academicDetails as $academicDetail) {
      $academicDetail -> insert($database, $this ->rollNo);
    }

    Database::setContacts($database, "StudentContact", "rollNo", $this -> rollNo, $this -> contacts);
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

  public function toHtml()
  {
    echo "<table class='table table-striped'>";
      
      echo "<thead>";  
        echo "<tr>";
        echo "<th style='width: 25%'></th>";
        echo "<th style='width: 75%'></th>";
        echo "</tr>";     
      echo "</thead>";
      
      echo "<tbody>";
        echo "<tr>";
        echo "<td>Roll Number</td>";
        echo "<td>".$this->rollNo."</td>";
        echo "</tr>";
        
        echo "<tr>";
        echo "<td>Name</td>";
        echo "<td>".$this->name."</td>";
        echo "</tr>";
        
        echo "<tr>";  
        echo "<td>Date of Birth</td>";
        echo "<td>".$this->dob."</td>";
        echo "</tr>";
        
        echo "<tr>";  
        echo "<td>Gender</td>";
        echo "<td>".$this->gender."</td>";
        echo "</tr>";
        
        echo "<tr>";  
        echo "<td>Institute Email ID</td>";
        echo "<td>".$this->instituteEmail."</td>";
        echo "</tr>";
        
        echo "<tr>";
        echo "<td>Other Email ID</td>";
        echo "<td>".$this->otherEmail."</td>";
        echo "</tr>";
        
        echo "<tr>";
        echo "<td>Category</td>";
        echo "<td>".$this->category."</td>";
        echo "</tr>";
        
        echo "<tr>";
        echo "<td>JEE/GATE Air</td>";
        echo "<td>".$this->jeeGateAir."</td>";
        echo "</tr>";
        
        echo "<tr>";
        echo "<td>Program</td>";
        echo "<td>";$this -> program -> toHtml();echo "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>Contact Numbers</td>";
        echo "<td>".implode(",", $this->contacts)."</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>Current Address</td>";
        echo "<td>".
              "<table class='table table-striped table-condensed'>";
                echo "<tbody>";
                    echo "<tr>";
                    echo "<td>Room Number</td>";
                    echo "<td>".$this->curAddrRoomNo."</td>";
                    echo "</tr>";
                    
                    echo "<tr>";
                    echo "<td>Hostel</td>";
                    echo "<td>".$this->curAddrHostelName."</td>";
                    echo "</tr>";
                echo "</tbody>";
        echo "</table></td>";
        echo "</tr>";
        
        echo "<tr>";
        echo "<td>Permanent Address</td>";
        echo "<td>";$this -> permanentAddress -> toHtml();echo "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>Academic Details</td>";
        echo "<td>";AcademicDetails::toHtml($this->academicDetails);echo "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>Current Academic Details</td>";
        echo "<td>";$this -> spi -> toHtml();echo "</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>Dream Company</td>";
        echo "<td>".$this -> dreamCompany."</td>";
        echo "</tr>";

        echo "<tr>";
        echo "<td>Resume</td>";
        echo '<td><a href="#">'.$this -> rollNo.'.pdf</a></td>';
        echo "</tr>";

      echo "</tbody>";
    echo "</table>";
  }

  public static function allToHtml()
  {

    $tablesFieldList = array_merge(self::$tablesFieldList,
     PermanentAddress::getTablesFieldList(),
     Program::getTablesFieldList(),
     Branch::getTablesFieldList(),
     array("curAddrHostelName")
     );

    $location = "( SELECT \"locationId\" AS \"curAddrHostel\",\"locationName\" AS \"curAddrHostelName\" FROM InstituteLocation ) AS W";
    $subQuery = Database::formSelectQuery(array_merge(array($location), self::$tables) , array(), array(), array())." AS T ";
    $query = Database::formSelectQuery(array_merge(array($subQuery), Program::getTablesName()),
                                         $tablesFieldList, array(), array());

    //Get Result from database
    $result =  $database -> executeQuery($query);
    $numberOfRows = pg_num_rows($result); 
    if ( $numberOfRows < 1)
      return 1;//die("No Student with this Roll Number");
    
    //Fill the Objects with details
    for ($i=0; $i < $numberOfRows; $i++) { 
      $detailArray = pg_fetch_array($result, $i, PGSQL_ASSOC);
      $students[$i] = new Student;
      //--Properties--
      $students[$i] -> setFromSuperset($detailArray);
      //$this -> spi -> read($database, array($this -> program -> getProgramType(), $primaryKeys[0]));
      $students[$i] -> contacts = Database::getContacts($database, "StudentContact", self::$primaryFieldList, $primaryKeys);
    }
    
    echo "<table class='table table-striped'>";
      
      echo "<thead>";  
        echo "<tr>";
        echo "<th>Name</th>";
        echo "<th>Roll Number</th>";
        echo "<tr>DOB</th>";
        echo "<th>Webmail</th>";
        echo "<th>Other Email ID</th>";
        echo "<th>Current Address</th>";
        echo "<td>Contacts</td>";
        echo "<th>Program</th>";
        echo "<th>Resume</th>";
        echo "</tr>";     
      echo "</thead>";
      
      echo "<tbody>";
      foreach ($students as $student) {
        echo "<tr>";
        echo "<td>".$student->name."</td>";
        echo "<td>".$student->rollNo."</td>";
        echo "<td>".$student->dob."</td>";
        echo "<td>".$student->instituteEmail."</td>";
        echo "<td>".$student->otherEmail."</td>";
        echo "<td>".$student->curAddrRoomNo." ".$student->curAddrHostelName."</td>";
        echo "<td>".implode(",", $student->contacts)."</td>";
        echo "<td>".$student -> program -> getProgramType()." ".$student -> program -> getBranch() -> getBranchName();echo "</td>";
        echo '<td><a href="#">'.$student -> rollNo.'.pdf</a></td>';
        echo "</tr>";
      }
      echo "</tbody>";
    echo "</table>";
  }

}



//Permanent Address Class
class PermanentAddress implements iObject, JsonSerializable{


//Static Members Variables
  /** @const **/
  private static $tables;
  /** @const **/
  private static $primaryFieldList;
  /** @const **/
  private static $tablesFieldList;


//Static Member Function
  public static function init(){
    self::$tables = array("Student");
    self::$primaryFieldList = array("rollNo");
    self::$tablesFieldList = array(
      "perAddrHouseNo",
      "perAddrStreet",
      "perAddrCity",
      "perAddrState",
      "perAddrCountry",
      "perAddrPincode"
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



//Object Attributes
  private $perAddrHouseNo;
  private $perAddrStreet;
  private $perAddrCity;
  private $perAddrState;
  private $perAddrCountry;
  private $perAddrPincode;


//Functions
  public function getHouseNo(){
    return $this -> perAddrHouseNo;
  }

  public function getStreet(){
    return $this -> perAddrStreet;
  }

  public function getCity(){
    return $this -> perAddrCity;
  }

  public function getState(){
    return $this -> perAddrState;
  }

  public function getCountry(){
    return $this -> perAddrCountry;
  }

  public function getPincode(){
    return $this -> perAddrPincode;
  }

  public function getValueArray(){
    return array($perAddrHouseNo, $perAddrStreet, $perAddrCity, $perAddrState, $perAddrCountry, $perAddrPincode);
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
      die("No Student with this Roll Number");
    $detailArray = pg_fetch_array($result, 0, PGSQL_ASSOC);

  //Fill Objects with details
    $this -> setFromSuperset($detailArray);

  }

  public function insert(Database $database){

  }

  public function toJson(){
    return json_encode($this, JSON_PRETTY_PRINT);
  }

  public function jsonSerialize()
  {
    return get_object_vars($this);
  }

  public function toHtml(){
       
      echo "<table class='table table-striped table-condensed'>";
      
        echo "<tbody>";
          echo "<tr>";
          echo "<td>House Number</td>";
          echo "<td>".$this->perAddrHouseNo."</td>";
          echo "</tr>";
          
          echo "<tr>";
          echo "<td>Street</td>";
          echo "<td>".$this->perAddrStreet."</td>";
          echo "</tr>";
          
          echo "<tr>";  
          echo "<td>City</td>";
          echo "<td>".$this->perAddrCity."</td>";
          echo "</tr>";
          
          echo "<tr>";  
          echo "<td>State</td>";
          echo "<td>".$this->perAddrState."</td>";
          echo "</tr>";
          
          echo "<tr>";  
          echo "<td>Country</td>";
          echo "<td>".$this->perAddrCountry."</td>";
          echo "</tr>";
            
          echo "<tr>";
          echo "<td>Pincode</td>";
          echo "<td>".$this->perAddrPincode."</td>";
          echo "</tr>";
        echo "</tbody>";
      
      echo "</table>";
  }  

}


//Academic Details Class
class AcademicDetails implements iObject, JsonSerializable{

//Static Members Variables
  /** @const **/
  private static $tables;
  /** @const **/
  private static $primaryFieldList;
  /** @const **/
  private static $tablesFieldList;


//Static Member Function
  public static function init(){
    self::$tables = array("AcademicDetails");
    self::$primaryFieldList = array("rollNo", "_level");
    self::$tablesFieldList = array(
      "_level",
      "boardDegree",
      "stream",
      "institute",
      "yearOfPassing",
      "cgpaPer"
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

//Object Attributes
  private $_level;
  private $boardDegree;
  private $stream;
  private $institute;
  private $yearOfPassing;
  private $cgpaPer;


//Functions
  public function getLevel(){
    return $this -> _level;
  }

  public function getBoardDegree(){
    return $this -> boardDegree;
  }

  public function getStream(){
    return $this -> stream;
  }

  public function getInstitute(){
    return $this -> institute;
  }

  public function getYearOfPassing(){
    return $this -> yearOfPassing;
  }

  public function getCgpaPer(){
    return $this -> cgpaPer;
  }

  public function getValueArray(){
    return array($_level, $boardDegree, $stream, $institute, $yearOfPassing, $cgpaPer);
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

  //Form Query
    $query = Database::formSelectQuery(self::$tables, self::$tablesFieldList, self::$primaryFieldList, $primaryKeys);

  //Get Result
    $result =  $database -> executeQuery($query);
    if (pg_num_rows($result) < 1)
      die("No Student with this Roll Number");
    $detailArray = pg_fetch_array($result, 0, PGSQL_ASSOC);

  //Fill Objects with details
    $this -> setFromSuperset($detailArray);
  }

  public function insert(Database $database, $rollNo){
    $query = Database::formInsertQuery(self::$tables[0] ,array_merge(self::$tablesFieldList, array("rollNo")), array(
                    $this -> _level,
                    $this -> boardDegree,
                    $this -> stream,
                    $this -> institute,
                    $this -> yearOfPassing,
                    $this -> cgpaPer,
                    $rollNo
             ));

    $result =  $database -> executeQuery($query);
  }

  public function toJson(){
    return json_encode($this, JSON_PRETTY_PRINT);
  }

  public function jsonSerialize()
  {
    return get_object_vars($this);
  }

  public static function toHtml($x)
  {
      echo "<table class='table table-striped table-condensed'>"; 
        echo "<thead>";  
          echo "<tr>";  
          echo "<th>Level</th>";  
          echo "<th>Board / Degree</th>";      
          echo "<th>Stream (Subject)</th>";  
          echo "<th>Institute</th>";
          echo "<th>YearofPassing</th>";
          echo "<th>Percentage/CPI</th>";     
          echo "</tr>";
        echo "</thead>";

        echo "<tbody>";
          $arrlength=count($x);
          for($x1=0;$x1<$arrlength;$x1++)
          {
            $obj = $x[$x1];
            
            echo "<tr>";  
            echo "<td>".$obj->_level."</td>";
            echo "<td>".$obj->boardDegree."</td>";   
            echo "<td>".$obj->stream."</td>";
            echo "<td>".$obj->institute."</td>";
            echo "<td>".$obj->yearOfPassing."</td>";
            echo "<td>".$obj->cgpaPer."</td>";
            echo "</tr>";
          }
        echo "</tbody>";
          
      echo "</table>";
  }
    
}


//Academic Details Class
class SPI implements iObject, JsonSerializable{

//Static Members Variables
  /** @const **/
  private static $BTechSPItable;
  /** @const **/
  private static $MasterSPItable;
  /** @const **/
  private static $DualDegreeSPItable;
  /** @const **/
  private static $primaryFieldList;
  /** @const **/
  private static $tablesFieldList;
  

//Static Member Functions
  public static function init(){
    self::$BTechSPItable = array("BTechSPI");
    self::$MasterSPItable = array("MasterSPI");
    self::$DualDegreeSPItable = array("DualDegreeSPI");
    self::$primaryFieldList = array("rollNo");
    self::$tablesFieldList = array(
      "cpi",
      "backlogNo",
    );
  }

  public static function getBTechSPITablesName(){
    return self::$BTechSPItable;
  }

  public static function getMasterSPITablesName(){
    return self::$MasterSPItable;
  }

  public static function getDualDegreeSPITablesName(){
    return self::$DualDegreeSPItable;
  }

  public static function getPrimaryFieldList(){
    return self::$primaryFieldList;
  }

  public static function getTablesFieldList(){
    return self::$tablesFieldList;
  }

//Object Attributes
  private $cpi;
  private $backlogNo;
  private $spi;
  private $spiNo;
  
//Functions
  public function getCpi(){
    return $this -> cpi;
  }

  public function getBacklogNo(){
    return $this -> backlogNo;
  }

  public function getSPIArray(){
    return $this -> spi;
  }

  public function getSPINo(){
    return $this -> spiNo;
  }

  public function getSPI($i){
    return $this -> spi[$i];
  }

  public function getValueArray(){
    return array_merge(array($this -> cpi, $this -> backlogNo), $this -> spi);
  }

  public function setFromSubset(array $details){
    /*Possibly incorrect
    foreach ($details as $key => $value) {
      if (preg_match('/^sem[0-9]/'pattern, $key, $match))
        $this -> sem[$match] = $value;
      else
        $this -> $key = $value;
    }*/
  }

  public function setFromSuperset(array $details){
    for ($i=1; $i <= $this -> getSPINo(); $i++) {
      $this -> spi["sem$i"] = $details["sem$i"]; 
    }
    foreach (self::getTablesFieldList() as $key) {
      $this -> $key = $details[$key];
    }
    //var_dump($this);
  }

  public function read(Database $database, array $primaryKeys){

    switch ($primaryKeys[0]) {
      case 'B.Tech.':
        $val = 7;
        $tables = self::$BTechSPItable;
        break;
     
      case 'Dual Degree':
       $val = 9;
       $tables = self::$DualDegreeSPItable;
       break;

      case 'M.Tech.':
        $val = 3;
        $tables = self::$MasterSPItable;
        break;

      case 'M.Sc.':
        $val = 3;
        $tables = self::$MasterSPItable;
        break;

      default:
        $val = 0;
        break;
    }
    if ($val == 0)
      return;
  
  $this -> spiNo = $val;

  $tablesFieldList = self::$tablesFieldList;
  for ($i=1; $i <= $this -> spiNo ; $i++) 
    array_push($tablesFieldList, "sem$i");
  
  //Form Query
    $query = Database::formSelectQuery($tables, $tablesFieldList, self::$primaryFieldList, array($primaryKeys[1]));

  //Get Result
    $result =  $database -> executeQuery($query);
    if (pg_num_rows($result) < 1)
      return 1;//die("No Student with this Roll Number");
      
    
    $detailArray = pg_fetch_array($result, 0, PGSQL_ASSOC);

  //Fill Objects with details
    $this -> setFromSuperset($detailArray);

    //var_dump(get_object_vars($this));
  }

  public function insert(Database $database){

  }

  public function toJson(){
    return json_encode($this, JSON_PRETTY_PRINT);
  }

  public function jsonSerialize()
  {
    return get_object_vars($this);
  }

  public function toHtml(){
      echo "<table class='table table-striped table-condensed'>"; 
        
        echo "<thead>";
          echo "<tr>";
          echo "<td width=\"20%\"></td>";
          echo "<td></td>";
          echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
          echo "<tr>";
          echo "<td>Current CPI</td>";
          echo "<td>".$this->cpi."</td>";
          echo "</tr>";
        
          echo "<tr>";
          echo "<td>Number of Backlogs</td>";
          echo "<td>".$this->backlogNo."</td>";
          echo "</tr>";

          echo "<tr>";
          echo "<td>SPIs</td>";
          echo "<td>";
            echo "<table class='table table-striped'>";
              echo "<thead>";
                echo "<tr>";
                for($x1=1;$x1<=$this->spiNo;$x1++)
                  echo "<th>SEM".$x1."</th>";       
                echo "</tr>";     
              echo "</thead>";

              $spiArr = (array)($this -> spi);
              echo "<tbody><tr>";
                for($x1=1;$x1<=$this->spiNo;$x1++)
                  echo "<td>".$spiArr["sem$x1"]."</td>";        
              echo "</tr></tbody>";
            echo "</table>";
          echo "</td>";
          echo "</tr>";
        echo "</tbody>";
    echo "</table>";
  }  
}


Student::init();
PermanentAddress::init();
AcademicDetails::init();
SPI::init();

?>
