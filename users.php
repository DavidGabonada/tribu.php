<?php
include "header.php";


  class User {
    // login
    function login($json){
      include 'connection.php';
      //{username:'pitok',password:12345}
      $json = json_decode($json, true);
      $sql = "SELECT * FROM tblusers 
              WHERE usr_name = :username AND usr_password = :password";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(':username', $json['username']);
      $stmt->bindParam(':password', $json['password']);
      $stmt->execute();
      $returnValue = $stmt->fetchAll(PDO::FETCH_ASSOC);
      unset($conn); unset($stmt);
      return json_encode($returnValue);
    }


    function save($json){
      //{username:'pitok',password:'12345', fullname:'PItok Batolata'}
      include 'connection.php';
      $json = json_decode($json, true);
      $sql = "INSERT INTO tblusers(usr_name, usr_password, usr_fullname)
        VALUES(:username, :password, :fullname)";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(':username', $json['username']);
      $stmt->bindParam(':password', $json['password']);
      $stmt->bindParam(':fullname', $json['fullname']);
      $stmt->execute();
      $returnValue = $stmt->rowCount() > 0 ? 1 : 0;
      unset($conn); unset($stmt);
      return json_encode($returnValue);
    }


    function getStudents($json){
      // {userId : 1}
      include 'connection.php';
      $json = json_decode($json, true);
      $sql = "SELECT * FROM tblstudent 
              WHERE student_userId = :userId
              ORDER BY student_Name";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(':userId', $json['userId']);
      $stmt->execute();
      $returnValue = $stmt->fetchAll(PDO::FETCH_ASSOC);
      unset($conn); unset($stmt);
      return json_encode($returnValue);
    }

    function getAllStudents(){
      include 'connection.php';
      $sql = "SELECT * FROM tblstudent
              ORDER BY student_Name";
      $stmt = $conn->prepare($sql);
      $stmt->execute();
      $returnValue = $stmt->fetchAll(PDO::FETCH_ASSOC);
      unset($conn); unset($stmt);
      return json_encode($returnValue);
    }
    
    


    // function adminlogin($json){
    //     include "connection-pdo.php";
    //     $json = json_decode($json, true);
    //     $sql = "SELECT * FROM tbluseradmin 
    //             WHERE usr_name = :username AND usr_password = :password";
    //     $stmt = $conn->prepare($sql);
    //     $stmt->bindParam(":username", $json['username']);
    //     $stmt->bindParam(":password", $json['password']);
    //     $stmt->execute();
    //     $returnValue = $stmt->fetchAll(PDO::FETCH_ASSOC);


    //     return json_encode($returnValue);
    // }


    // function getUsers($json) {
    //   include 'connection-pdo.php';
    //   $json = json_decode($json, true);
    //   $sql = "SELECT * FROM tblusers ORDER BY usr_id";
    //   $stmt = $conn->prepare($sql);
    //   $stmt->execute();
    //   $returnValue = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //   unset($conn); unset($stmt);
    //   return json_encode($returnValue);
    // }
  }


  //submitted by the client - operation and json data
  $json = isset($_POST["json"]) ? $_POST["json"] : "0";
  $operation = isset($_POST["operation"]) ? $_POST["operation"] : "0";


  $user = new User();
  switch($operation){
    case "login":
      echo $user->login($json);
      break;
    case "save":
      echo $user->save($json);
      break;
    case "getStudents":
      echo $user->getStudents($json);
      break;
    case "getAllStudents":	
      echo $user->getAllStudents();
      break;
    // case "adminlogin":
    //   echo $user->adminlogin($json);
    //   break;
    // case "getUsers":
    //   echo $user->getUsers($json);
    //   break;
  }
?>
