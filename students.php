<?php
include "header.php";

class Student {
    function get($json) {
        include 'connection.php';
        $json = json_decode($json, true);
        $sql = "SELECT * FROM tblstudent ORDER BY student_Id";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $returnValue = $stmt->fetchAll(PDO::FETCH_ASSOC);
        unset($conn); unset($stmt);
        return json_encode($returnValue);
    }

    function gettribu($json) {
        include 'connection.php';
        $json = json_decode($json, true);
        $sql = "SELECT * FROM tbltribu ORDER BY tribu_Id";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $returnValue = $stmt->fetchAll(PDO::FETCH_ASSOC);
        unset($conn); unset($stmt);
        return json_encode($returnValue);
    }

    function getyr($json) {
        include 'connection.php';
        $json = json_decode($json, true);
        $sql = "SELECT * FROM tblyear ORDER BY year_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $returnValue = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    function getattendance($json){
        include 'connection.php';
        $json = json_decode($json, true);
        $sql = "INSERT INTO tblattendance(attendance_id, attendance_studentsId, attendance_timein, attendance_timeout)
        VALUES(:id, :studentsId, :timein, :timeout)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $json['id']);
        $stmt->bindParam(':studentsId', $json['studentsId']);
        $stmt->bindParam(':timein', $json['timein']);
        $stmt->bindParam(':timeout', $json['timeout']);
        $stmt->execute();
        $returnValue = $stmt->rowCount() > 0 ? 1 : 0;
        unset($conn); unset($stmt);
        return json_encode($returnValue);
    }

    function getAllStudentByTribu($json){
        // {tribuId : 1}
        include 'connection.php';
        $data = json_decode($json, true);
        $sql = " SELECT a.attendance_timein, a.attendance_timeout, b.student_Name, c.year_type FROM tblattedance a
                INNER JOIN tblstudent b ON b.student_Id = a.attendance_studentId
                INNER JOIN tblyear c ON c.year_id = b.student_yrId
                WHERE b.student_tribuId = :tribuId";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':tribuId', $data['tribuId']);
        $stmt->execute();
        $returnValue = $stmt->fetchAll(PDO::FETCH_ASSOC);
        unset($conn); unset($stmt);
        return json_encode($returnValue);
    }

    function getReport(){
        include 'connection.php';
        $sql = "SELECT a.attendance_timein, a.attendance_timeout, b.student_Name, c.year_type FROM tblattedance a
                INNER JOIN tblstudent b ON b.student_Id = a.attendance_studentId
                INNER JOIN tblyear c ON c.year_id = b.student_yrId";	
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
        $sql = "INSERT INTO tblstudent(student_Name, student_schoolId, student_tribuId, student_yrId, student_userId)
          VALUES(:name, :schoolId, :tribuId, :yearId, :userId)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $json['name']);
        $stmt->bindParam(':schoolId', $json['schoolId']);
        $stmt->bindParam(':tribuId', $json['tribuId']);
        $stmt->bindParam(':yearId', $json['yearId']);
        $stmt->bindParam(':userId', $json['userId']);
        $stmt->execute();
        $returnValue = $stmt->rowCount() > 0 ? 1 : 0;
        unset($conn); unset($stmt);
        return json_encode($returnValue);
    }
}

$json = isset($_POST["json"]) ? $_POST["json"] : "0";
$operation = isset($_POST["operation"]) ? $_POST["operation"] : "0";

$student = new Student();

// Ensure operation is set before proceeding
if ($operation) {
    switch($operation){
        case "get":
            echo $student->get($json);
            break;
        case "gettribu":
            echo $student->gettribu($json);
            break;
        case "getyear":
            echo $student->getyr($json);
            break;
        case "getStudents":
            echo $student->getStudents($json);
            break;
        case "save":
            echo $student->save($json);
            break;
        case "getattendance":
            echo $student->getattendance($json);
            break;
        case "getAllStudentByTribu":
            echo $student->getAllStudentByTribu($json);
            break;
        case "getReport":	
            echo $student->getReport();
            break;
        default:
            echo json_encode(["error" => "Invalid operation"]);
            break;
    }
}
?>
