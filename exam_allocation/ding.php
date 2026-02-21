<?php

use function PHPSTORM_META\type;

include 'config/db_connect.php';
include 'config/functions.php';
include 'routes/algorithms.php';
$examType = $_SESSION['examType'] ?? null;
if (isset($_SESSION['seating_data']) and isset($examType)) {
  $seatingData = $_SESSION['seating_data'];
  $eid = $_SESSION['eid'];
  $rooms = $_SESSION['rooms'];
  $rooms = implode(", ", $rooms);
  $res = null;
  if ($examType == 1) {
    $res = getExamInfo($conn, $eid);
  } elseif ($examType == 2) {
    $res = getUniversityExamInfo($conn, $eid);
  } else {
    die("Allocation Failed! Unknown Exam Type");
    exit;
  }
  $seatingResult = [];
  if ($examType == 1) {
    while ($row = $res->fetch_assoc()) {
      $edate = $row['edate'];
      $session = $row['session'];
      $edata = $seatingData['S' . $eid . '_' . $row['edate'] . '_' . $row['session']];
      if ($edata['no_sems'] === 1) {
        $sem =  $edata['groups'][0][0];
        $grid1 = $edata['grids']['grid1'];
        $branches = [];
        foreach ($grid1 as $item) {
          $branches[] = $item['branch'];
        }
        $shuffleOrder1 = $branches;

        $grid2 = $edata['grids']['grid2'];
        $branches = [];
        foreach ($grid2 as $item) {
          $branches[] = $item['branch'];
        }
        $shuffleOrder2 = $branches;
        $results = algoOne($conn, $sem, $shuffleOrder1, $shuffleOrder2, $rooms, $edate, $session, $eid);
        $seatingResult = array_merge($seatingResult, $results);
      }
      if ($edata['no_sems'] === 2) {
        $sem1 =  $edata['groups'][0][0];
        $sem2 =  $edata['groups'][0][1];
        $grid1 = $edata['grids']['grid1'];
        $branches = [];
        foreach ($grid1 as $item) {
          $branches[] = $item['branch'];
        }
        $shuffleOrder1 = $branches;

        $grid2 = $edata['grids']['grid2'];
        $branches = [];
        foreach ($grid2 as $item) {
          $branches[] = $item['branch'];
        }
        $shuffleOrder2 = $branches;
        $results = algoTwo($conn, $sem1, $sem2, $shuffleOrder1, $shuffleOrder2, $rooms, $edate, $session, $eid);
        $seatingResult = array_merge($seatingResult, $results);
      }

      if ($edata['no_sems'] === 3) {
        $sem3 =  $edata['groups'][0][0];
        $sem1 =  $edata['groups'][1][0];
        $sem2 =  $edata['groups'][1][1];
        $grid1 = $edata['grids']['grid1'];
        $grid2 = $edata['grids']['grid2'];
        $grid3 = $edata['grids']['grid3'];
        $branches = [];
        foreach ($grid1 as $item) {
          $branches[] = $item['branch'];
        }
        $shuffleOrder1 = $branches;

        $branches = [];
        foreach ($grid2 as $item) {
          $branches[] = $item['branch'];
        }
        $shuffleOrder2 = $branches;

        $branches = [];
        foreach ($grid3 as $item) {
          $branches[] = $item['branch'];
        }
        $shuffleOrder3 = $branches;


        $results = algoThree($conn, $sem1, $sem2, $sem3, $shuffleOrder1, $shuffleOrder2, $shuffleOrder3, $rooms, $edate, $session, $eid);
        $seatingResult = array_merge($seatingResult, $results);
      }

      if ($edata['no_sems'] === 4) {
        $sem1 =  $edata['groups'][0][0];
        $sem2 =  $edata['groups'][0][1];
        $sem3 =  $edata['groups'][1][0];
        $sem4 =  $edata['groups'][1][1];
        $grid1 = $edata['grids']['grid1'];
        $grid2 = $edata['grids']['grid2'];
        $grid3 = $edata['grids']['grid3'];
        $grid4 = $edata['grids']['grid4'];
        $branches = [];
        foreach ($grid1 as $item) {
          $branches[] = $item['branch'];
        }
        $shuffleOrder1 = $branches;

        $branches = [];
        foreach ($grid2 as $item) {
          $branches[] = $item['branch'];
        }
        $shuffleOrder2 = $branches;

        $branches = [];
        foreach ($grid3 as $item) {
          $branches[] = $item['branch'];
        }
        $shuffleOrder3 = $branches;

        $branches = [];
        foreach ($grid4 as $item) {
          $branches[] = $item['branch'];
        }
        $shuffleOrder4 = $branches;
        $results = algoFour($conn, $sem1, $sem2, $sem3, $sem4, $shuffleOrder1, $shuffleOrder2, $shuffleOrder3, $shuffleOrder4, $rooms, $edate, $session, $eid);
        $seatingResult = array_merge($seatingResult, $results);
      }
    }
  } elseif ($examType == 2) {
    while ($row = $res->fetch_assoc()) {
      $edate = $row['edate'];
      $session = $row['session'];
      $edata = $seatingData['S' . $eid . '_' . $row['edate'] . '_' . $row['session']];
      $grid1 = $edata['grids']['grid1'];
      $branches = [];
      foreach ($grid1 as $item) {
        $branches[] = $item['branch'];
      }
      $shuffleOrder = $branches;
      $results = algoUniOne($conn, $shuffleOrder, $rooms, $edate, $session, $eid);
      $seatingResult = array_merge($seatingResult, $results);
    }
  } else {
    die("Allocation Failed! Unknown Exam Type");
    exit;
  }
  date_default_timezone_set('Asia/Kolkata');
  $currentDateTime = date("d-m-Y H:i:s");
  $stmt = $conn->prepare(
    "INSERT INTO seating_allocation_definition (eid, created_at) 
     VALUES (?, ?)"
  );
  $stmt->bind_param("is", $eid, $currentDateTime);
  $stmt->execute();
  $aid = $conn->insert_id;
  mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

  try {
    $conn->begin_transaction();
    $stmt = $conn->prepare(
      "INSERT INTO seating_allocation_data
          (aid, reg_no, room, edate, session, electiveCourseId, seat)
          VALUES (?, ?, ?, ?, ?, ?, ?)"
    );

    foreach ($seatingResult as $seatData) {
      $stmt->bind_param(
        "issssss",
        $aid,
        $seatData['reg_no'],
        $seatData['room'],
        $seatData['edate'],
        $seatData['session'],
        $seatData['elective'],
        $seatData['seat']
      );
      $stmt->execute();
    }

    $conn->commit();
  } catch (mysqli_sql_exception $e) {
    $conn->rollback();
    error_log("Seating allocation insert failed: " . $e->getMessage());
    throw $e;
  }
  header("Location: overview.php");
}
