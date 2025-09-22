<?php
include 'db_connect.php'; // database connection

if (isset($_POST['upload'])) {
    $filename = $_FILES['file']['tmp_name'];

    if ($_FILES['file']['size'] > 0) {
        $file = fopen($filename, "r");

        // Skip header row (if your CSV has headers)
        fgetcsv($file);

        while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
            $Block    = $data[0];
            $Room_no  = $data[1];
            $Capacity = $data[2];
            $Type     = $data[3];

            // Insert or Update if Room_no already exists
            $stmt = $conn->prepare(
                "INSERT INTO rooms (Block, Room_no, Capacity, Type) 
                 VALUES (?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE 
                    Block = VALUES(Block),
                    Capacity = VALUES(Capacity),
                    Type = VALUES(Type)"
            );
            $stmt->bind_param("ssis", $Block, $Room_no, $Capacity, $Type);

            try {
                $stmt->execute();
                $message = "CSV data imported successfully!";
            } catch (mysqli_sql_exception $e) {
                $message = "Error: " . $e->getMessage();
            }
        }

        fclose($file);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Room CSV</title>
</head>
<body>
    <h2>Upload Room Details (CSV)</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="file" accept=".csv" required>
        <button type="submit" name="upload">Upload</button>
    </form>

    <?php if (!empty($message)) echo "<p>$message</p>"; ?>
</body>
</html>
