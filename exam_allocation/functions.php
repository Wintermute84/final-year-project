<?php
  function getRooms($conn, $block = null) {
      if ($block != 'All') {
          $stmt = $conn->prepare("SELECT * FROM rooms WHERE Block = ? ORDER BY Rid ASC");
          $stmt->bind_param("s", $block);
          $stmt->execute();
          $result = $stmt->get_result();
      } else {
          $result = $conn->query("SELECT * FROM rooms ORDER BY Rid ASC");
      }
      return $result;
  }

  function deleteRoom($conn,$id) {
    $stmt = $conn->prepare("DELETE FROM rooms WHERE Room_no = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $stmt->close();
  }

  function importRoomsFromCSV($conn, $fileTmpName, $block) {
    if ($_FILES['file']['size'] > 0) {
        $file = fopen($fileTmpName, "r");

        fgetcsv($file);

        while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
            $Block    = $data[0];
            $Room_no  = $data[1];
            $Capacity = $data[2];
            $Type     = $data[3];

            $stmt = $conn->prepare(
                "INSERT INTO rooms (Block, Room_no, Capacity, Type) 
                 VALUES (?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE 
                    Block = VALUES(Block),
                    Capacity = VALUES(Capacity),
                    Type = VALUES(Type)"
            );
            $stmt->bind_param("ssis", $Block, $Room_no, $Capacity, $Type);
            $stmt->execute();
        }

        fclose($file);

        return true;
    }
    return false;
}

    function importStudentsFromCSV($conn,$fileTmpName){
        if ($_FILES['file']['size'] > 0) {
        $file = fopen($fileTmpName, "r");

        fgetcsv($file);

        while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
            $regno    = $data[0];
            $rollno  = $data[1];
            $name = $data[2];
            $branch = $data[3];
            $semester = $data[4];
            $el1 = $data[5];
            $el2 = $data[6];
            $el3 = $data[7];

            $stmt = $conn->prepare(
                "INSERT INTO students (reg_no, rollno, name, branch, semester, elective_1, elective_2, elective_3) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE 
                    elective_1 = VALUES(elective_1),
                    elective_2 = VALUES(elective_2),
                    elective_3 = VALUES(elective_3)
                "
            );
            $stmt->bind_param("sississs", $regno, $rollno, $name, $branch, $semester, $el1, $el2, $el3);
            $stmt->execute();
        }

        fclose($file);

        return true;
    }
    return false;
    }

    function getStudents($conn, $semester) {
        if ($semester != 'All') {
          $stmt = $conn->prepare("SELECT distinct branch, semester FROM students WHERE semester = ?");
          $stmt->bind_param("i", $semester);
          $stmt->execute();
          $result = $stmt->get_result();
      } else {
          $result = $conn->query("SELECT distinct branch, semester FROM students order by semester");
      }
      return $result;
    }

    function getStudentData($conn, $semester, $branch){
        $stmt = $conn->prepare("SELECT * FROM students WHERE semester = ? and branch = ?");
        $stmt->bind_param("is", $semester, $branch);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    function deleteStudentData($conn){
        $stmt = $conn->prepare("DELETE FROM students");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }
?>
