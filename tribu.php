<?php
include "header.php";


  class Tribu {
    function get() {
        include 'connection.php';
        // $json = json_decode($json, true);
        $sql = "SELECT * FROM tbltribu ORDER BY tribu_Id";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $returnValue = $stmt->fetchAll(PDO::FETCH_ASSOC);
        unset($conn); unset($stmt);
        return json_encode($returnValue);
    }
  
    function save($json){
        //{username:'pitok',password:'12345', fullname:'PItok Batolata'}
        include 'connection.php';
        $json = json_decode($json, true);
        $sql = "INSERT INTO tbltribu(tribu_Name) VALUES(:name)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $json['name']);
        $stmt->execute();
        $returnValue = $stmt->rowCount() > 0 ? 1 : 0;
        unset($conn); unset($stmt);
        return json_encode($returnValue);
    }
  }


//   if ($_SERVER['REQUEST_METHOD'] == 'GET'){
//     $operation = $_GET['operation'];
//     $json = $_GET['json'];
//   }else if($_SERVER['REQUEST_METHOD'] == 'POST'){
//     $operation = $_POST['operation'];
//     $json = $_POST['json'];
//   }

 $operation = isset($_POST["operation"]) ? $_POST["operation"] : "0";
 $json = isset($_POST["json"]) ? $_POST["json"] : "0";


  $tribu = new Tribu();
  switch($operation){
    case "getTribu":
      echo $tribu->get();
      break;
    case "save":
      echo $student->save($json);
      break;
  }
?>
