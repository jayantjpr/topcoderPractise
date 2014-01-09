<?php
if(isset($_POST["name"])&&isset($_POST["user_nm"])&&isset($_POST["pass"])&&!empty($_POST["name"])&&!empty($_POST["user_nm"])&&!empty($_POST["pass"]))
{
    $name = $_POST["name"];
    $user_nm = $_POST["user_nm"];
    $pass =md5($_POST["pass"]);
    $query="SELECT `user_nm` from registrant WHERE handle='".$user_nm."'";
    $result=pg_query($query);
    if(pg_num_rows($result)==0){
        $query="INSERT INTO registrant()";
    }else if(pg_num_rows($result)==NULL){
        
    }else{
        $error="querry failed";
    }
    
}
?>