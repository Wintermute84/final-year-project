<?php
require_once __DIR__ . '/config/functions.php';

$username = "root";
$password = "";
$dbname = "exam";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if (isset($_POST["upload_faculty_csv"])) {
    if (isset($_FILES["faculty_csv"]) && $_FILES["faculty_csv"]["error"] == 0) {
        $fileTmpPath = $_FILES["faculty_csv"]["tmp_name"];
        if (importFacultyDataCSV($conn, $fileTmpPath)) {
            $message .= "<p style='color:green;'>Faculty Data CSV uploaded and processed successfully!</p>";
        }
        else {
            $message .= "<p style='color:red;'>Failed to process Faculty Data CSV.</p>";
        }
    }
    else {
        $message .= "<p style='color:red;'>Please select a valid CSV file for Faculty Data.</p>";
    }
}

if (isset($_POST["upload_timetable_pdf"])) {
    if (isset($_FILES["timetable_pdf"]) && $_FILES["timetable_pdf"]["error"] == 0) {
        $fileTmpPath = $_FILES["timetable_pdf"]["tmp_name"];
        $originalName = $_FILES["timetable_pdf"]["name"];

        if (importFacultyTimeTablePDF($conn, $fileTmpPath, $originalName) === false) {
            $message .= "<p style='color:red;'>Failed to process Faculty Time Table PDF.</p>";
        }
    }
    else {
        $message .= "<p style='color:red;'>Please select a valid PDF file for the Time Table.</p>";
    }
}

if (isset($_POST["upload_timetable_csv"])) {
    if (isset($_FILES["timetable_csv"]) && $_FILES["timetable_csv"]["error"] == 0) {
        $fileTmpPath = $_FILES["timetable_csv"]["tmp_name"];
        if (importFacultyTimeTableCSV($conn, $fileTmpPath)) {
            $message .= "<p style='color:green;'>Faculty Time Table CSV uploaded and mapped to database successfully!</p>";
        }
        else {
            $message .= "<p style='color:red;'>Failed to process Faculty Time Table CSV.</p>";
        }
    }
    else {
        $message .= "<p style='color:red;'>Please select a valid CSV file for the Time Table.</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Upload Functions</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
        }

        .upload-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .upload-section h3 {
            margin-top: 0;
        }

        input[type="submit"] {
            margin-top: 10px;
            padding: 5px 15px;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <h2>Test Uploading Faculty Data and Timetable</h2>

    <div>
        <?php echo $message; ?>
    </div>

    <div class="upload-section">
        <h3>1. Upload Faculty Data (CSV)</h3>
        <p>Ensure the CSV format is: <strong>Faculty, Designation, Total Duty, Is Available</strong></p>
        <form action="test_upload.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="faculty_csv" accept=".csv" required>
            <br>
            <input type="submit" name="upload_faculty_csv" value="Upload Faculty Data">
        </form>
    </div>

    <div class="upload-section">
        <h3>2. Convert Timetable (PDF to CSV)</h3>
        <p>Upload the PDF. The system will convert it and prompt you to download the CSV file.</p>
        <form action="test_upload.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="timetable_pdf" accept=".pdf" required>
            <br>
            <input type="submit" name="upload_timetable_pdf" value="Convert & Download CSV">
        </form>
    </div>

    <div class="upload-section">
        <h3>3. Upload Converted Timetable (CSV)</h3>
        <p>Upload the CSV file you downloaded in Step 2. This will map the faculties and insert the timetable into the
            database.</p>
        <form action="test_upload.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="timetable_csv" accept=".csv" required>
            <br>
            <input type="submit" name="upload_timetable_csv" value="Upload Timetable Database">
        </form>
    </div>

</body>

</html>
