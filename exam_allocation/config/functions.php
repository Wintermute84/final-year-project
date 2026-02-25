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
    $stmt = $conn->prepare("DELETE FROM rooms WHERE Room_no = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $stmt->close();
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
                     VALUES (?, ?, ?, ?, ?)"
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
