<?php
include 'db_connect.php'; // your DB connection

$sql = "SELECT * FROM rooms ORDER BY Rid ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Rooms</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
        }
        table {
            border-collapse: collapse;
            width: 80%;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background: #f2f2f2;
        }
        h2 {
            margin-bottom: 20px;
        }
        a {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            background: #007bff;
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
        }
        a:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <h2>Room List</h2>
    <a href="upload_rooms.php">➕ Upload New CSV</a>

    <table>
        <tr>
            <th>RID</th>
            <th>Block</th>
            <th>Room Number</th>
            <th>Capacity</th>
            <th>Type</th>
        </tr>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['Rid'] ?></td>
                    <td><?= $row['Block'] ?></td>
                    <td><?= $row['Room_no'] ?></td>
                    <td><?= $row['Capacity'] ?></td>
                    <td><?= $row['Type'] ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5">No rooms found.</td></tr>
        <?php endif; ?>
    </table>
</body>
</html>
