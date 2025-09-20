<?php
  function getRooms($conn, $block = null) {
      if ($block) {
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
?>
