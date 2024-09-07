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

    function getStudents(){
      // {userId : 1}
      include 'connection.php';
      $sql = "SELECT * FROM tblstudent 
              ORDER BY student_Name";
      $stmt = $conn->prepare($sql);
      $stmt->execute();
      $returnValue = $stmt->fetchAll(PDO::FETCH_ASSOC);
      unset($conn); unset($stmt);
      return json_encode($returnValue);
    }
    // function getattendance($json){
    //     include 'connection.php';
    //     // {"id":1, "studentsId":"20022","timein":"04:56:23"
        
    //     $json = json_decode($json, true);
    //     $sql = "INSERT INTO tblattedance(attendance_studentId, attendance_timein)
    //     VALUES(:studentsId, :timein)";
    //     $stmt = $conn->prepare($sql);
       
    //     $stmt->bindParam(':studentsId', $json['studentsId']);
    //     $stmt->bindParam(':timein', $json['timein']);
 
    //     $stmt->execute();
    //     $returnValue = $stmt->rowCount() > 0 ? 1 : 0;
    //     unset($conn); unset($stmt);
    //     return json_encode($returnValue);
    // }
    // function gettimeout($json){
    //     include 'connection.php';
    //     $json = json_decode($json, true);
    //     $sql = "";
    //     $stmt = $conn->prepare($sql);

    //     $stmt->bindParam(':studentsId', $json['studentsId']);
    //     $stmt->bindParam(':timeout', $json['timeout']);
 
    //     $stmt->execute();
    //     $returnValue = $stmt->rowCount() > 0 ? 1 : 0;
    //     unset($conn); unset($stmt);
    //     return json_encode($returnValue);
    // }
    function getattendance($json){
        include 'connection.php';
        // {"studentsId":1}
    
        // Decode the JSON input
        $json = json_decode($json, true);
        
        $studentsId = $json['studentsId'];
        // $currentTimein = $json['timein'];
    
        // Query to check if the student has already checked in within the last 24 hours
        $checkSql = "SELECT attendance_timein 
                     FROM tblattedance 
                     WHERE attendance_studentId = :studentsId
                     ORDER BY attendance_timein DESC 
                     LIMIT 1";
    
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bindParam(':studentsId', $studentsId);
        $checkStmt->execute();
    
        $lastAttendance = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
        // Check if there's any existing attendance record
        if ($lastAttendance) {
            $lastTimein = strtotime($lastAttendance['attendance_timein']);
            $currentTimeinTimestamp = strtotime($currentTimein);
    
            // Calculate the time difference in hours
            // $hoursDifference = ($currentTimeinTimestamp - $lastTimein) / 3600;
    
            // If the student has checked in within the last 24 hours, return an error message
            if ($hoursDifference < 24) {
                return json_encode([
                    'status' => 0,
                    'message' => 'You have already checked in within the last 24 hours.'
                ]);
            }
        }
    
        // Prepare the SQL statement to insert the attendance record
        $sql = "INSERT INTO tblattedance(attendance_studentId)
                VALUES(:studentsId)";
        
        $stmt = $conn->prepare($sql);
    
        // Bind parameters
        $stmt->bindParam(':studentsId', $studentsId);
        // Execute the statement
        $stmt->execute();
    
        // Check if the insert was successful
        $returnValue = $stmt->rowCount() > 0 ? 1 : 0;
    
        // Clean up resources
        unset($conn); unset($stmt);
    
        // Return the result as a JSON response
        return json_encode([
            'status' => $returnValue,
            'message' => $returnValue ? 'Check-in successful.' : 'Failed to check in.'
        ]);
    }
    
    function gettimeout($json){
        include 'connection.php';
    
        // Decode the JSON input
        $json = json_decode($json, true);
        // {"attendanceId":1,"timeout":"2024-09-06 11:26:21"}
        // First, check if the student has already "timed in"
        $checkSql = "SELECT * FROM tblattedance WHERE attendance_studentId = :studentsId";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bindParam(':studentsId', $json['studentsId']);
        $checkStmt->execute();
    
        if ($checkStmt->rowCount() > 0) {
            // If a record exists where "timein" is recorded and "timeout" is not, update it with the "timeout"
            $sql = "UPDATE tblattedance SET attendance_timeout = :timeout 
                    WHERE attendance_studentId = :studentsId";
            
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':studentsId', $json['studentsId']);
            $stmt->bindParam(':timeout', $json['timeout']);
            
            // Execute the update statement
            $stmt->execute();
            $returnValue = $stmt->rowCount() > 0 ? 1 : 0;
        } else {
            // No matching "timein" record, return a failure code (e.g., 0)
            $returnValue = 0;
        }
    
        // Clean up resources
        unset($conn); unset($stmt); unset($checkStmt);
    
        // Return the result as a JSON response
        return json_encode([
            'status' => $returnValue,
            'message' => $returnValue ? 'Timeout recorded successfully.' : 'Failed to record timeout.'
        ]);
    }
    
    

    function getAllStudentByTribu($json){
        // {tribuId : 1}
        include 'connection.php';
        $data = json_decode($json, true);
        $sql = "SELECT a.attendance_timein, a.attendance_timeout, b.student_Name, b.student_yrId, c.year_type FROM tblattedance a
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

    function getallattendace($json){
        include 'connection.php';
        $data = json_decode($json, true);
        $sql = "SELECT a.attendance_timein, a.attendance_timeout, b.student_Name, b.student_tribuId, c.year_type FROM tblattedance a
                INNER JOIN tblstudent b ON b.student_Id = a.attendance_studentId
                INNER JOIN tblyear c ON c.year_id = b.student_yrId";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $returnValue = $stmt->fetchAll(PDO::FETCH_ASSOC);
        unset($conn); unset($stmt);
        return json_encode($returnValue);
    }

    function saveStudent($json){
        // {"fullname": "Joe Doe", "username": "jdoe", "tribe_id": 1, "password": "password123", "yearlevel_id": 1, "school_id": "02-2021-03668"}
        include 'connection.php';
        $json = json_decode($json, true);
        $conn->beginTransaction();
        try {
        $sql = "INSERT INTO tblusers (usr_name, usr_password, usr_fullname)
        VALUES (:username, :password, :fullname)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $json['username']);
        $stmt->bindParam(':password', $json['password']);
        $stmt->bindParam(':fullname', $json['fullname']);
        $stmt->execute();
    
        $newId = $conn->lastInsertId();
            $sql = "INSERT INTO tblstudent (student_Name, student_tribuId, student_schoolId, student_yrId, student_userId) 
                VALUES (:fullname, :tribe_id, :school_id, :yearlevel_id, :newId)";
    
                
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':fullname', $json['fullname']);
        $stmt->bindParam(':tribe_id', $json['tribe_id']);  // Referencing tribe ID
        $stmt->bindParam(':yearlevel_id', $json['yearlevel_id']);  // Referencing year level ID
        $stmt->bindParam(':school_id', $json['school_id']);
        $stmt->bindParam(':newId', $newId);
        $stmt->execute();
        $conn->commit();
        $returnValue = $stmt->rowCount() > 0 ? 1 : 0;
        unset($conn); unset($stmt);
        return json_encode($returnValue);
        } catch (PDOException $th) {
            $conn->rollBack();
            return $th;
        }
    
    
        
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
        $sql = "INSERT INTO tblstudent(student_Name, student_tribuId, student_schoolId, student_yrId, student_userId)
          VALUES(:name, :tribuId, :schoolId, :yearId, :userId)";
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
} // student nga class



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
            echo $student->getStudents();
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
        case "saveStudent":
            echo $student->saveStudent($json);
            break;
        case "gettimeout":
            echo $student->gettimeout($json);
            break;
        case "getallattendace":
            echo $student->getallattendace($json);
            break;
        default:
            echo json_encode(["error" => "Invalid operation"]);
            break;
    }
}
?>
