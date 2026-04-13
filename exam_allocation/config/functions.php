<?php
function getRooms($conn, $block = null)
{
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

function getBlocks($conn)
{
    $result = $conn->query("SELECT distinct Block FROM rooms");
    return $result;
}

function deleteRoom($conn, $id)
{
    if ($id == "All") {
        $stmt = $conn->prepare("DELETE FROM rooms");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    } else {
        $stmt = $conn->prepare("DELETE FROM rooms WHERE Room_no = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $stmt->close();
    }
}

function importRoomsFromCSV($conn, $fileTmpName, $block)
{
    if ($_FILES['file']['size'] > 0) {
        $file = fopen($fileTmpName, "r");

        fgetcsv($file);

        while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
            $data = array_map('trim', $data);
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

function importStudentsFromCSV($conn, $fileTmpName)
{
    if ($_FILES['file']['size'] > 0) {
        $file = fopen($fileTmpName, "r");

        fgetcsv($file);

        while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
            $data = array_map('trim', $data);
            $regno    = $data[0];
            $rollno  = $data[1];
            $name = $data[2];
            $branch = $data[3];
            $semester = $data[4];
            $el1 = $data[5] ?? null;
            $el2 = $data[6] ?? null;
            $el3 = $data[7] ?? null;
            $minor = $data[8] ?? null;

            $stmt = $conn->prepare(
                "INSERT INTO students (reg_no, rollno, name, branch, semester, elective_1, elective_2, elective_3, minor) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE 
                    elective_1 = VALUES(elective_1),
                    elective_2 = VALUES(elective_2),
                    elective_3 = VALUES(elective_3),
                    branch = VALUES(branch),
                    minor = VALUES(minor)
                "
            );
            $stmt->bind_param("sississss", $regno, $rollno, $name, $branch, $semester, $el1, $el2, $el3, $minor);
            $stmt->execute();
        }

        fclose($file);

        return true;
    }
    return false;
}

function getStudents($conn, $semester)
{
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

function getStudentData($conn, $semester, $branch)
{
    $stmt = $conn->prepare("SELECT * FROM students WHERE semester = ? and branch = ?");
    $stmt->bind_param("is", $semester, $branch);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}

function deleteStudentData($conn, $branch = null, $sem = null)
{
    if ($sem === 'All') {
        $stmt = $conn->prepare("DELETE FROM students");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    } else {
        $stmt = $conn->prepare("DELETE FROM students WHERE semester = ? and branch = ?");
        $stmt->bind_param("is", $sem, $branch);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }
}

function addExamDefinition($conn, $filename)
{
    $examName = $_POST['examNameInput'];
    $examType = $_POST['examTypeInput'];
    $startDate = $_POST['startDateInput'];
    $endDate = $_POST['endDateInput'];
    $stmt = $conn->prepare("INSERT INTO exam_definition (ename, etype, sdate, edate) VALUES (?,?,?,?)");
    $stmt->bind_param("siss", $examName, $examType, $startDate, $endDate);
    $stmt->execute();
    $last_id = $conn->insert_id;
    if ($_FILES['time-table-upload-file']['size'] > 0 || $_FILES['appearing-list-upload-file']['size'] > 0) {
        $file = fopen($filename, "r");

        fgetcsv($file);
        if ($examType == 1) {
            while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
                $data = array_map('trim', $data);
                $edate = $data[0];
                $sess = $data[1];
                $ccode = $data[2];
                $sem = $data[3];
                $branch = $data[4];

                $branchArray = array_map('trim', explode(',', $branch));

                foreach ($branchArray as $branches) {
                    $branches = trim($branches);
                    $stmts = $conn->prepare(
                        "INSERT INTO exam_time_table(eid,edate,session,ccode,sem,branch) 
                        VALUES (?, ?, ?, ?, ?, ?)
                        "
                    );
                    $stmts->bind_param("isssis", $last_id, $edate, $sess, $ccode, $sem, $branches);
                    $stmts->execute();
                }
            }
        } elseif ($examType == 2) {
            while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
                $data = array_map('trim', $data);
                $studId = $data[0];
                $branch = $data[1];
                $ccode = $data[2];
                $edate = $data[3];
                $sess = $data[4];

                $stmts = $conn->prepare(
                    "INSERT INTO appearing_list(eid,student,branch,ccode,edate,session) 
                            VALUES (?, ?, ?, ?, ?, ?)
                            "
                );
                $stmts->bind_param("isssss", $last_id, $studId, $branch, $ccode, $edate, $sess);
                $stmts->execute();
            }
        }

        fclose($file);
    }
    return true;
}

function getExams($conn, $etype)
{
    if ($etype == "University Exam") {
        $result = $conn->query("SELECT * FROM exam_definition where etype = 2");
    } elseif ($etype == "Internal Exam") {
        $result = $conn->query("SELECT * FROM exam_definition where etype = 1");
    } else {
        $result = $conn->query("SELECT * FROM exam_definition");
    }
    return $result;
}

function importCoursesFromCSV($conn, $fileTmpName)
{
    if ($_FILES['file']['size'] > 0) {
        $file = fopen($fileTmpName, "r");

        fgetcsv($file);

        while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
            $data = array_map('trim', $data);
            $ccode    = $data[0];
            $cname  = $data[1];
            $is_elective = $data[2];
            $sem = $data[3];
            $branch = $data[4];
            $branchArray = array_map('trim', explode(',', $branch));

            foreach ($branchArray as $branches) {
                $branches = trim($branches);
                $stmt = $conn->prepare(
                    "INSERT INTO courses (ccode, cname, sem, branch, is_elective) 
                    VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE 
                    ccode = VALUES(ccode),
                    sem = VALUES(sem),
                    cname = VALUES(cname),
                    branch = VALUES(branch),
                    is_elective = VALUES(is_elective)"
                );
                $stmt->bind_param("ssisi", $ccode, $cname, $sem, $branches, $is_elective);
                $stmt->execute();
            }
        }

        fclose($file);

        return true;
    }
    return false;
}

function getCourses($conn, $semester)
{
    if ($semester != 'All') {
        $stmt = $conn->prepare("SELECT distinct branch, sem FROM courses WHERE sem = ?");
        $stmt->bind_param("i", $semester);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query("SELECT distinct branch, sem FROM courses order by sem");
    }
    return $result;
}

function getCourseData($conn, $semester, $branch)
{
    $stmt = $conn->prepare("SELECT * FROM courses WHERE sem = ? and branch = ?");
    $stmt->bind_param("is", $semester, $branch);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}

function getExamTimeTableData($conn, $eid)
{
    $stmt = $conn->prepare("SELECT * FROM exam_time_table WHERE eid = ?");
    $stmt->bind_param("i", $eid);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}

function deleteExam($conn, $eid)
{
    $stmt = $conn->prepare("Delete FROM exam_definition WHERE eid = ?");
    $stmt->bind_param("i", $eid);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmts = $conn->prepare("Delete FROM exam_time_table WHERE eid = ?");
    $stmts->bind_param("i", $eid);
    $stmts->execute();
    $results = $stmt->get_result();
    return ($results && $result);
}

function getExamInfo($conn, $eid)
{
    $stmt = $conn->prepare("SELECT DISTINCT edate,session FROM exam_time_table WHERE eid = ?");
    $stmt->bind_param("i", $eid);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}

function deleteCourses($conn, $sem, $branch)
{
    if ($sem === 'All') {
        $stmt = $conn->prepare("DELETE FROM courses");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    } else {
        $stmt = $conn->prepare("DELETE FROM courses WHERE sem = ? and branch=?");
        $stmt->bind_param("is", $sem, $branch);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }
}


function getSeatingAllocations($conn)
{
    $stmt = $conn->prepare("SELECT * FROM seating_allocation_definition sad JOIN exam_definition ed on sad.eid=ed.eid");
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}


function getSeatingExamData($conn, $aid)
{
    $stmt = $conn->prepare("SELECT distinct sad.edate, sad.session, e.ename,e.etype FROM seating_allocation_data sad JOIN seating_allocation_definition saad ON sad.aid=saad.aid JOIN exam_definition e ON saad.eid=e.eid where sad.aid = ?");
    $stmt->bind_param("i", $aid);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}

function getSeatingRoomData($conn, $aid, $edate, $session)
{
    $stmt = $conn->prepare("SELECT distinct room,Rid,Capacity,Type,edate,aid,session FROM seating_allocation_data sad JOIN rooms on sad.room=rooms.Room_no where aid = ? and edate = ? and session = ?");
    $stmt->bind_param("iss", $aid, $edate, $session);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}


function deleteSeatingData($conn, $id)
{
    $stmt = $conn->prepare("Delete FROM seating_allocation_definition WHERE aid = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmts = $conn->prepare("Delete FROM seating_allocation_data WHERE aid = ?");
    $stmts->bind_param("i", $id);
    $stmts->execute();
    $results = $stmt->get_result();
    return ($results && $result);
}

function getAppearingListData($conn, $eid)
{
    $stmt = $conn->prepare("Select * from appearing_list where eid = ?");
    $stmt->bind_param("i", $eid);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}

function getUniversityExamInfo($conn, $eid)
{
    $stmt = $conn->prepare("Select distinct edate, session from appearing_list where eid = ?");
    $stmt->bind_param("i", $eid);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result;
}

function convertCsvExamTimeTable($filename)
{
    $python = "python";
    $script = "./scripts/format_timetable.py";
    $filePath = escapeshellarg($filename);
    $command = "$python $script $filePath";
    exec($command, $output, $return_var);
    if ($return_var !== 0) {
        die("Python Error:<br>" . implode("<br>", $output));
    }

    $outputFile =  trim(end($output));

    if (!$outputFile || !file_exists($outputFile)) {
        die("Output file not found");
    }

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . basename($outputFile) . '"');
    header('Content-Length: ' . filesize($outputFile));

    readfile($outputFile);
    exit;
}

function importFacultyDataCSV($conn, $fileTmpName)
{
    if (file_exists($fileTmpName)) {
        $file = fopen($fileTmpName, "r");

        fgetcsv($file);

        while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
            $faculty = trim($data[0] ?? '');
            if (empty($faculty))
                continue;

            $designation = trim($data[1] ?? 'Assistant');

            $total_duty = isset($data[2]) && $data[2] !== '' ? intval($data[2]) : 0;
            $is_available = isset($data[3]) && trim($data[3]) === '0' ? 0 : 1;

            $check = $conn->prepare("SELECT fid FROM faculty_data WHERE faculty = ?");
            $check->bind_param("s", $faculty);
            $check->execute();
            $check_res = $check->get_result();
            if ($check_res->num_rows > 0) {
                $row = $check_res->fetch_assoc();
                $fid = $row['fid'];
                $update = $conn->prepare("UPDATE faculty_data SET designation = ?, total_duty = ?, is_available = ? WHERE fid = ?");
                $update->bind_param("siii", $designation, $total_duty, $is_available, $fid);
                $update->execute();
            }
            else {
                $stmt = $conn->prepare(
                    "INSERT INTO faculty_data (faculty, designation, total_duty, is_available) VALUES (?, ?, ?, ?)"
                );
                $stmt->bind_param("ssii", $faculty, $designation, $total_duty, $is_available);
                $stmt->execute();
            }
        }
        fclose($file);
        return true;
    }
    return false;
}

function importFacultyTimeTableCSV($conn, $fileTmpName)
{
    if (file_exists($fileTmpName)) {
        $file = fopen($fileTmpName, "r");

        fgetcsv($file);

        while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
            $faculty = $data[0] ?? '';
            $day = $data[1] ?? '';
            $start_time = $data[2] ?? '';
            $end_time = $data[3] ?? '';
            $branch = $data[4] ?? '';
            $sem = $data[5] ?? '';

            if (empty($faculty) || empty($day) || empty($start_time) || empty($end_time)) {
                continue;
            }

            $stmt = $conn->prepare("SELECT fid FROM faculty_data WHERE faculty = ?");
            $stmt->bind_param("s", $faculty);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $fid = $row['fid'];
            }
            else {
                $designation = 'Assistant';
                $insert_fid = $conn->prepare("INSERT INTO faculty_data (faculty, designation) VALUES (?, ?)");
                $insert_fid->bind_param("ss", $faculty, $designation);
                $insert_fid->execute();
                $fid = $insert_fid->insert_id;
            }

            if (preg_match('/\d+/', $sem, $matches)) {
                $sem_val = intval($matches[0]);
            }
            else {
                $sem_val = 0;
            }

            $start_time = date("H:i:s", strtotime($start_time));
            $end_time = date("H:i:s", strtotime($end_time));

            $stmt = $conn->prepare(
                "INSERT INTO faculty_time_table (fid, faculty, day, start_time, end_time, branch, sem) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("isssssi", $fid, $faculty, $day, $start_time, $end_time, $branch, $sem_val);
            $stmt->execute();
        }

        fclose($file);

        return true;
    }
    return false;
}

function importFacultyTimeTablePDF($conn, $fileTmpName, $originalFileName)
{
    $uploadDir = __DIR__ . "/../uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $pdfPath = $uploadDir . time() . "_" . basename($originalFileName);
    if (is_uploaded_file($fileTmpName)) {
        move_uploaded_file($fileTmpName, $pdfPath);
    }
    else {
        copy($fileTmpName, $pdfPath);
    }

    $csvPath = $pdfPath . ".csv";

    $pythonScript = realpath(__DIR__ . "/../scripts/convert.py");

    $command = escapeshellcmd("python \"$pythonScript\" \"$pdfPath\" \"$csvPath\"");
    exec($command, $output, $return_var);

    if ($return_var === 0 && file_exists($csvPath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . basename($originalFileName, '.pdf') . '_converted.csv"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($csvPath));
        flush();
        readfile($csvPath);

        exit;
    }
    else {
        error_log("PDF to CSV conversion failed: " . implode("\n", $output));

        @unlink($pdfPath);
        return false;
    }
}

function getAvailableFacultyList($conn, $edate, $session, $max_associate_dutycap)
{
    $stmt = $conn->prepare("SELECT distinct branch, sem, time, day FROM exam_time_table WHERE edate = ?");
    $stmt->bind_param("s", $edate);
    $stmt->execute();
    $res = $stmt->get_result();

    $exam_sems_only = [];
    $exam_time_str = null;
    $exam_day = null;
    while ($row = $res->fetch_assoc()) {
        if (!in_array($row['sem'], $exam_sems_only)) {
            $exam_sems_only[] = $row['sem'];
        }
    }

    $stmt_time = $conn->prepare("SELECT time, day FROM exam_time_table WHERE edate = ? AND session = ? LIMIT 1");
    $stmt_time->bind_param("ss", $edate, $session);
    $stmt_time->execute();
    $time_res = $stmt_time->get_result();
    if ($t_row = $time_res->fetch_assoc()) {
        $exam_time_str = $t_row['time'];
        $exam_day = ucfirst(strtolower(substr($t_row['day'], 0, 3)));
    }

    if (!$exam_time_str)
        return [];

    $parts = explode('-', $exam_time_str);
    if (count($parts) != 2)
        return [];

    $exam_start = trim($parts[0]);
    $exam_end = trim($parts[1]);

    $start_ts = strtotime("1970-01-01 $exam_start");
    $end_ts = strtotime("1970-01-01 $exam_end");

    $duty_start_ts = $start_ts - (30 * 60);
    $duty_end_ts = $end_ts + (30 * 60);

    $query = "SELECT * FROM faculty_data WHERE is_available IS NULL OR is_available != 0";
    $res = $conn->query($query);
    $faculty_pool = [];
    while ($row = $res->fetch_assoc()) {
        $faculty_pool[$row['fid']] = $row;
    }

    $fids = array_keys($faculty_pool);
    foreach ($fids as $fid) {
        $faculty_pool[$fid]['is_impacted'] = false;
    }

    $fids_str = implode(',', $fids);

    $stmt = $conn->prepare("SELECT fid, start_time, end_time, branch, sem FROM faculty_time_table WHERE fid IN ($fids_str) AND day = ?");
    $stmt->bind_param("s", $exam_day);
    $stmt->execute();
    $tt_res = $stmt->get_result();

    $busy_fids = [];
    while ($tt_row = $tt_res->fetch_assoc()) {
        $fid = $tt_row['fid'];
        if (in_array($fid, $busy_fids) || !isset($faculty_pool[$fid]))
            continue;

        $c_start_ts = strtotime("1970-01-01 " . $tt_row['start_time']);
        $c_end_ts = strtotime("1970-01-01 " . $tt_row['end_time']);

        if ($c_start_ts < $duty_end_ts && $c_end_ts > $duty_start_ts) {
            if (trim($tt_row['branch']) === '')
                continue;

            $class_sem_only = $tt_row['sem'];
            if (in_array($class_sem_only, $exam_sems_only)) {
                $faculty_pool[$fid]['is_impacted'] = true;
            }
            else {
                $busy_fids[] = $fid;
            }
        }
    }

    foreach ($busy_fids as $busy_fid) {
        unset($faculty_pool[$busy_fid]);
    }

    if (empty($faculty_pool))
        return [];

    return array_values($faculty_pool);
}

function getGlobalFacultyMatrix($conn, $eid = null)
{
    $eid_clause = "";
    if ($eid !== null) {
        $eid_clause = " WHERE eid = " . intval($eid);
    }

    $etype = 1;
    if ($eid !== null) {
        $etype_res = $conn->query("SELECT etype FROM exam_definition WHERE eid = " . intval($eid));
        if ($etype_res && $etype_row = $etype_res->fetch_assoc()) {
            $etype = (int) $etype_row['etype'];
        }
    }

    if ($etype === 2) {
        $all_slots_query = "
            SELECT 
                edate, 
                IF(CAST(SUBSTRING_INDEX(session, ':', 1) AS UNSIGNED) < 12, 'FN', 'AN') as session,
                session as time,
                UPPER(DAYNAME(STR_TO_DATE(edate, '%d-%m-%Y'))) as day,
                GROUP_CONCAT(DISTINCT CONCAT(branch,'_',sem)) as branch_sems, 
                GROUP_CONCAT(DISTINCT sem) as sems 
            FROM appearing_list 
            $eid_clause
            GROUP BY edate, session
            ORDER BY STR_TO_DATE(edate, '%d-%m-%Y') ASC, session ASC
        ";

        $date_sems_query = "SELECT edate, (SELECT GROUP_CONCAT(DISTINCT sem) FROM appearing_list) as sems FROM appearing_list GROUP BY edate";

    } else {
        $all_slots_query = "SELECT edate, session, time, day, GROUP_CONCAT(CONCAT(branch,'_',sem)) as branch_sems, GROUP_CONCAT(sem) as sems 
                            FROM exam_time_table 
                            $eid_clause
                            GROUP BY edate, session, time, day
                            ORDER BY STR_TO_DATE(edate, '%d-%m-%Y') ASC, STR_TO_DATE(SUBSTRING_INDEX(time, '-', 1), '%H:%i') ASC";

        $date_sems_query = "SELECT edate, GROUP_CONCAT(sem) as sems FROM exam_time_table $eid_clause GROUP BY edate";
    }

    $slots_res = $conn->query($all_slots_query);

    $date_sems_res = $conn->query($date_sems_query);
    $date_sems_map = [];
    while ($row = $date_sems_res->fetch_assoc()) {
        $date_sems_map[$row['edate']] = array_unique(explode(',', $row['sems']));
    }

    $all_slots = [];
    while ($slot = $slots_res->fetch_assoc()) {
        $all_slots[] = [
            'edate' => $slot['edate'],
            'session' => $slot['session'],
            'time' => $slot['time'],
            'day' => ucfirst(strtolower(substr($slot['day'], 0, 3))),
            'sems_only' => $date_sems_map[$slot['edate']] ?? []
        ];
    }

    if (empty($all_slots))
        return [];

    $query = "SELECT * FROM faculty_data WHERE is_available IS NULL OR is_available != 0";
    $res = $conn->query($query);
    $faculty_pool = [];
    while ($row = $res->fetch_assoc()) {
        $faculty_pool[$row['fid']] = $row;
        $fids[] = $row['fid'];
    }

    if (empty($faculty_pool))
        return [];
    $fids_str = implode(',', $fids);

    $stmt = $conn->query("SELECT fid, day, start_time, end_time, branch, sem FROM faculty_time_table WHERE fid IN ($fids_str)");
    $timetable_map = [];
    while ($t_row = $stmt->fetch_assoc()) {
        $timetable_map[$t_row['fid']][$t_row['day']][] = $t_row;
    }

    $existing_duties_query = "
        SELECT fd.fid, DATE_FORMAT(fd.date, '%d-%m-%Y') as edate, fd.slot as session
        FROM faculty_duty fd
        JOIN seating_allocation_definition def ON fd.aid = def.aid
    ";
    if ($eid !== null) {
        $existing_duties_query .= " WHERE def.eid != " . intval($eid);
    }

    $exist_res = $conn->query($existing_duties_query);
    $busy_slots_map = [];
    if ($exist_res) {
        while ($row = $exist_res->fetch_assoc()) {
            $slot_key = $row['edate'] . ' ' . $row['session'];
            $busy_slots_map[$row['fid']][$slot_key] = true;
        }
    }

    foreach ($faculty_pool as $fid => &$fac) {
        $total_free = 0;
        $matrix = [];

        foreach ($all_slots as $slot) {
            $slot_key = $slot['edate'] . ' ' . $slot['session'];
            $s_parts = explode('-', $slot['time']);

            $s_start_ts = strtotime("1970-01-01 " . trim($s_parts[0]));
            $s_end_ts = strtotime("1970-01-01 " . trim($s_parts[1]));

            $s_d_start = $s_start_ts - (30 * 60);
            $s_d_end = $s_end_ts + (30 * 60);

            if (isset($busy_slots_map[$fid][$slot_key])) {
                $matrix[$slot_key] = '';
                continue;
            }

            $is_busy = false;
            if (isset($timetable_map[$fid][$slot['day']])) {
                foreach ($timetable_map[$fid][$slot['day']] as $cls) {
                    if (trim($cls['branch']) === '')
                        continue;

                    $c_start_ts = strtotime("1970-01-01 " . $cls['start_time']);
                    $c_end_ts = strtotime("1970-01-01 " . $cls['end_time']);

                    if ($c_start_ts < $s_d_end && $c_end_ts > $s_d_start) {
                        $cls_sem = $cls['sem'];
                        if (!in_array($cls_sem, $slot['sems_only'])) {
                            $is_busy = true;
                            break;
                        }
                    }
                }
            }

            if (!$is_busy) {
                $matrix[$slot_key] = '1';
                $total_free++;
            } else {
                $matrix[$slot_key] = '';
            }
        }

        $fac['total_free_slots'] = $total_free;
        $fac['matrix'] = $matrix;
    }
    unset($fac);
    return $faculty_pool;
}

function allocateGlobalInvigilation($conn, $eid, $max_associate_dutycap)
{
    $conn->query("
        UPDATE faculty_data fd
        JOIN (
            SELECT fdut.fid, COUNT(*) as c 
            FROM faculty_duty fdut
            JOIN seating_allocation_definition def ON fdut.aid = def.aid
            WHERE def.eid = " . intval($eid) . "
            GROUP BY fdut.fid
        ) d ON fd.fid = d.fid
        SET fd.total_duty = GREATEST(0, fd.total_duty - d.c)
    ");

    $conn->query("
        DELETE fdut FROM faculty_duty fdut
        JOIN seating_allocation_definition def ON fdut.aid = def.aid
        WHERE def.eid = " . intval($eid) . "
    ");

    $faculty_pool = getGlobalFacultyMatrix($conn, $eid);
    if (empty($faculty_pool))
        return [];

    $faculty_duty_during_this_exam = [];
    foreach ($faculty_pool as $fid => $fac) {
        $faculty_duty_during_this_exam[$fid] = 0;
    }

    $duty_matrix = [];
    foreach ($faculty_pool as $fid => $fac) {
        $duty_matrix[$fid] = [
            'S.No' => 0,
            'Faculty Name' => $fac['faculty'],
            'Designation' => $fac['designation'],
            'Total Lifetime Duty' => $fac['total_duty'],
            'Total Free Slots' => $fac['total_free_slots'],
        ];
    }

    $etype = 1;
    $etype_res = $conn->query("SELECT etype FROM exam_definition WHERE eid = " . intval($eid));
    if ($etype_res && $row = $etype_res->fetch_assoc()) {
        $etype = (int) $row['etype'];
    }

    $stmt = $conn->prepare("
        SELECT sad.edate, sad.session, sad.room, r.Type as room_type, MAX(sad.aid) as aid, COUNT(sad.reg_no) as N 
        FROM seating_allocation_data sad
        JOIN seating_allocation_definition def ON sad.aid = def.aid
        LEFT JOIN rooms r ON sad.room = r.Room_no
        WHERE def.eid = ?
        GROUP BY sad.edate, sad.session, sad.room, r.Type
    ");
    $stmt->bind_param("i", $eid);
    $stmt->execute();
    $res = $stmt->get_result();

    $slot_requirements = [];
    $room_requirements = [];
    while ($row = $res->fetch_assoc()) {
        $slot_key = trim($row['edate']) . ' ' . trim($row['session']);
        $N = (int) $row['N'];
        $rtype = isset($row['room_type']) ? $row['room_type'] : '';

        if ($etype === 2) {
            $req = (strcasecmp($rtype, 'Drawing') === 0) ? 2 : 1;
        } else {
            $req = ($N > 35) ? 2 : 1;
        }

        if (!isset($slot_requirements[$slot_key])) {
            $slot_requirements[$slot_key] = 0;
        }
        $slot_requirements[$slot_key] += $req;

        if (!isset($room_requirements[$slot_key])) {
            $room_requirements[$slot_key] = [];
        }
        $room_requirements[$slot_key][] = ['room' => $row['room'], 'req' => $req, 'aid' => $row['aid']];
    }

    $all_slot_keys = array_keys($slot_requirements);
    foreach ($faculty_pool as $fid => $fac) {
        foreach ($all_slot_keys as $sk) {
            $duty_matrix[$fid][$sk] = '';
        }
    }

    $slot_availability = [];
    foreach ($slot_requirements as $slot_key => $req) {
        $avail_count = 0;
        foreach ($faculty_pool as $fid => $fac) {
            if (isset($fac['matrix'][$slot_key]) && $fac['matrix'][$slot_key] === '1') {
                $avail_count++;
            }
        }
        $slot_availability[$slot_key] = $avail_count;
    }

    $sorted_slots = array_keys($slot_requirements);
    usort($sorted_slots, function ($a, $b) use ($slot_availability) {
        return $slot_availability[$a] <=> $slot_availability[$b];
    });

    $allocations = [];
    $assigned_for_slot = [];
    $slot_assigned_count = [];

    foreach ($sorted_slots as $slot_key) {
        if ($slot_requirements[$slot_key] == 0)
            continue;

        $parts = explode(' ', $slot_key);
        if (count($parts) < 2)
            continue;
        $s_edate = $parts[0];
        $s_session = $parts[1];

        $rooms_for_slot = isset($room_requirements[$slot_key]) ? $room_requirements[$slot_key] : [];
        $assigned_for_slot[$slot_key] = [];

        $candidates = [];
        foreach ($faculty_pool as $fid => $fac) {
            if (isset($fac['matrix'][$slot_key]) && $fac['matrix'][$slot_key] === '1') {
                $has_worked_today = 0;
                foreach ($allocations as $alloc) {
                    if ($alloc['fid'] == $fid && trim($alloc['edate']) === $s_edate) {
                        $has_worked_today = 1;
                        break;
                    }
                }

                $candidates[$fid] = [
                    'fid' => $fid,
                    'designation' => $fac['designation'],
                    'total_free_slots' => $fac['total_free_slots'],
                    'total_duty' => $fac['total_duty'],
                    'duty_during_this_exam' => $faculty_duty_during_this_exam[$fid],
                    'has_worked_today' => $has_worked_today
                ];
            }
        }

        $sortCandidates = function ($a, $b) {
            if ($a['has_worked_today'] !== $b['has_worked_today']) {
                return $a['has_worked_today'] <=> $b['has_worked_today'];
            }
            if ($a['total_duty'] !== $b['total_duty']) {
                return $a['total_duty'] <=> $b['total_duty'];
            }
            if ($a['total_free_slots'] !== $b['total_free_slots']) {
                return $a['total_free_slots'] <=> $b['total_free_slots'];
            }

            $a_is_asst = (stripos($a['designation'], 'Assistant') !== false) ? 1 : 0;
            $b_is_asst = (stripos($b['designation'], 'Assistant') !== false) ? 1 : 0;
            if ($a_is_asst !== $b_is_asst) {
                return $b_is_asst <=> $a_is_asst;
            }

            return 0;
        };

        $valid_candidates = array_filter($candidates, function ($c) use ($max_associate_dutycap) {
            $is_asst = stripos($c['designation'], 'Assistant') !== false;
            return $is_asst || ($c['duty_during_this_exam'] < $max_associate_dutycap);
        });

        usort($valid_candidates, $sortCandidates);

        if (!isset($slot_assigned_count[$slot_key])) {
            $slot_assigned_count[$slot_key] = 0;
        }

        foreach ($rooms_for_slot as &$room_data) {
            $room_req = $room_data['req'];
            $req_aid = $room_data['aid'];

            while ($room_req > 0 && !empty($valid_candidates)) {
                $chosen = null;
                $found_idx = -1;
                foreach ($valid_candidates as $idx => $cand) {
                    if (!isset($assigned_for_slot[$slot_key][$cand['fid']])) {
                        $chosen = $cand;
                        $found_idx = $idx;
                        break;
                    }
                }

                if ($chosen !== null) {
                    unset($valid_candidates[$found_idx]);
                } else {
                    break;
                }

                $fid = $chosen['fid'];
                $assigned_for_slot[$slot_key][$fid] = true;

                $allocations[] = [
                    'edate' => $s_edate,
                    'session' => $s_session,
                    'aid' => $req_aid,
                    'fid' => $fid
                ];

                $duty_matrix[$fid][$slot_key] = '1';
                $slot_assigned_count[$slot_key]++;

                $faculty_duty_during_this_exam[$fid] += 1;
                $faculty_pool[$fid]['total_duty'] += 1;

                $room_req--;
            }
        }
    }

    $shortfalls = [];
    foreach ($slot_requirements as $slot_key => $required) {
        $assigned = $slot_assigned_count[$slot_key] ?? 0;
        if ($assigned < $required) {
            $shortfalls[] = [
                'slot' => $slot_key,
                'required' => $required,
                'assigned' => $assigned,
                'missing' => $required - $assigned,
            ];
        }
    }


    $stmt = $conn->prepare("INSERT INTO faculty_duty (fid, aid, date, slot) VALUES (?, ?, ?, ?)");
    foreach ($allocations as $alloc) {
        $db_date = date('Y-m-d', strtotime($alloc['edate']));
        $stmt->bind_param("iiss", $alloc['fid'], $alloc['aid'], $db_date, $alloc['session']);
        try {
            $stmt->execute();
            $update_td = $conn->prepare("UPDATE faculty_data SET total_duty = total_duty + 1 WHERE fid = ?");
            $update_td->bind_param("i", $alloc['fid']);
            $update_td->execute();
        } catch (Exception $e) {
        }
    }

    $csv_data = [];
    $sno = 1;
    foreach ($duty_matrix as $fid => $dm) {
        $dm['S.No'] = $sno++;
        $csv_data[] = $dm;
    }

    return [
        'allocations' => $allocations,
        'csv_matrix' => $csv_data,
        'slot_keys' => $all_slot_keys,
        'shortfalls' => $shortfalls,
    ];
}

function resetAllFacultyDuty($conn)
{
    $conn->query("UPDATE faculty_data SET total_duty = 0");
    $conn->query("TRUNCATE TABLE faculty_duty");
}

function getDistinctExamSlots($conn)
{
    $sql = "SELECT DISTINCT edate, session, time, day FROM exam_time_table ORDER BY STR_TO_DATE(edate, '%d-%m-%Y') ASC, session ASC";
    return $conn->query($sql);
}

