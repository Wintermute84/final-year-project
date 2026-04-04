<?php
require_once __DIR__ . '/config/db_connect.php';
require_once __DIR__ . '/config/functions.php';

$success_msg = '';
$error = '';
$shortfalls = [];

if (isset($_POST['reset_duty'])) {
    try {
        resetAllFacultyDuty($conn);
        $success_msg = "All faculty_data.total_duty is now reset to 0 in the database.";
    } catch (Exception $e) {
        $error = "Reset failed: " . $e->getMessage();
    }
}

if (isset($_POST['download_after_warning']) && isset($_SESSION['pending_csv'])) {
    $pending = $_SESSION['pending_csv'];
    unset($_SESSION['pending_csv']);
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $pending['filename'] . '"');
    echo $pending['content'];
    exit;
}

function buildCsvContent($csv_matrix, $matrix_slots, $eid) {
    ob_start();
    $output = fopen('php://output', 'w');
    if (!empty($csv_matrix)) {
        $headers = ['S.No', 'Faculty Name', 'Designation', 'Total Free Slots', 'Total Duty'];
        foreach ($matrix_slots as $m) $headers[] = $m;
        fputcsv($output, $headers);
        foreach ($csv_matrix as $row_data) {
            $duties_count = 0;
            foreach ($matrix_slots as $m) {
                if (isset($row_data[$m]) && $row_data[$m] === '1') $duties_count++;
            }
            $prev_duty = intval($row_data['Total Lifetime Duty']);
            $new_duty  = $prev_duty + $duties_count;
            $row = [$row_data['S.No'], $row_data['Faculty Name'], $row_data['Designation'], $row_data['Total Free Slots'], $new_duty];
            foreach ($matrix_slots as $m) $row[] = isset($row_data[$m]) ? $row_data[$m] : '';
            fputcsv($output, $row);
        }
    } else {
        fputcsv($output, ['No allocations were made. Note: Seating data must exist for EID ' . $eid]);
    }
    fclose($output);
    return ob_get_clean();
}

if (isset($_POST['generate_allocation'])) {
    $eid = intval($_POST['eid']);
    $max_associate_dutycap = intval($_POST['max_cap'] ?? 2);
    try {
        $result_data = allocateGlobalInvigilation($conn, $eid, $max_associate_dutycap);
        $csv_matrix  = $result_data['csv_matrix'];
        $matrix_slots = $result_data['slot_keys'];
        $shortfalls  = $result_data['shortfalls'] ?? [];

        $csv_content = buildCsvContent($csv_matrix, $matrix_slots, $eid);
        $filename    = "final_assigned_duty_matrix_eid_{$eid}.csv";

        if (empty($shortfalls)) {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            echo $csv_content;
            exit;
        }

        $_SESSION['pending_csv'] = ['content' => $csv_content, 'filename' => $filename];
        $_SESSION['pending_eid'] = $eid;

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$exams_res = getExams($conn, 'All');
$all_exams = [];
if ($exams_res) {
    while ($row = $exams_res->fetch_assoc()) {
        $all_exams[] = $row;
    }
}

$selected_eid = isset($_POST['eid']) && $_POST['eid'] !== '' ? intval($_POST['eid']) : null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Duty Allocation Panel</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="./styles/output.css">
</head>

<body x-data="{on: false}" class="bg-black h-screen flex flex-col relative text-white">

  <main class="flex-1 flex overflow-hidden">
    <section class="w-full h-full overflow-y-auto p-6 bg-black relative">
      <div
        style="display: flex; flex-direction: column; gap: 1rem; border-bottom: 1px solid #333; padding-bottom: 1rem; margin-bottom: 1.5rem;">
        <div>
          <h2 class="text-2xl font-bold mb-2 text-[#18C088]">Invigilation Duty Allocation</h2>
          <p class="text-gray-400">
            <?= $selected_eid ? "Allocating duties for Exam ID: " . $selected_eid : "Select an exam to generate and download the duty assignment matrix." ?>
          </p>
        </div>

        <?php if (!empty($error)): ?>
          <div style="color:#ff4c4c; background:rgba(255,76,76,0.1); padding:10px; border-radius:4px; border-left:4px solid #ff4c4c;">
            <strong>Error:</strong><br><?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>
        <?php if (!empty($success_msg)): ?>
          <div style="color:#18C088; background:rgba(24,192,136,0.1); padding:10px; border-radius:4px; border-left:4px solid #18C088; font-size:14px;">
            <strong>Success:</strong><br><?= htmlspecialchars($success_msg) ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($shortfalls)): ?>
          <div style="background:rgba(255,165,0,0.08); border:1px solid #e67e22; border-left:4px solid #e67e22; border-radius:6px; padding:16px;">
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:10px;">
              <svg fill="none" stroke="#e67e22" viewBox="0 0 24 24" style="width:22px;height:22px;flex-shrink:0;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
              </svg>
              <strong style="color:#e67e22; font-size:15px;">Faculty Shortfall Detected — <?= count($shortfalls) ?> slot(s) are under-staffed</strong>
            </div>
            <table style="width:100%; border-collapse:collapse; font-size:13px; margin-bottom:14px;">
              <thead>
                <tr style="border-bottom:1px solid #555; color:#aaa; text-align:left;">
                  <th style="padding:6px 10px;">Slot (Date &amp; Session)</th>
                  <th style="padding:6px 10px; text-align:center;">Required</th>
                  <th style="padding:6px 10px; text-align:center;">Assigned</th>
                  <th style="padding:6px 10px; text-align:center;">Shortfall</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($shortfalls as $sf): ?>
                  <tr style="border-bottom:1px solid #333;">
                    <td style="padding:6px 10px; color:#fff;"><?= htmlspecialchars($sf['slot']) ?></td>
                    <td style="padding:6px 10px; text-align:center; color:#ccc;"><?= $sf['required'] ?></td>
                    <td style="padding:6px 10px; text-align:center; color:#ccc;"><?= $sf['assigned'] ?></td>
                    <td style="padding:6px 10px; text-align:center; color:#e67e22; font-weight:bold;">-<?= $sf['missing'] ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            <p style="color:#aaa; font-size:12px; margin-bottom:12px;">
              The allocation has already been saved to the database. The CSV reflects the actual assignments. You may download it below.
            </p>
            <form method="POST" action="test_allocation.php">
              <button type="submit" name="download_after_warning"
                style="background:#e67e22; color:white; border:none; padding:10px 20px; border-radius:6px; font-weight:bold; cursor:pointer; font-size:14px;">
                  Download CSV Anyway
              </button>
            </form>
          </div>
        <?php endif; ?>

        
        <form method="POST" action="test_allocation.php" id="examFilterForm" class="flex items-center gap-3 bg-[#222] p-3 rounded-lg border border-[#333]">
          <label class="text-gray-300 font-medium">Select Exam:</label>
          <select name="eid" onchange="document.getElementById('examFilterForm').submit()" class="bg-[#333] text-white p-2 rounded-md border border-[#444] outline-none focus:border-[#18C088]">
            <option value="">-- Select Exam --</option>
            <?php foreach ($all_exams as $ex): ?>
              <option value="<?= $ex['eid'] ?>" <?= $selected_eid === intval($ex['eid']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($ex['ename']) ?> (EID: <?= $ex['eid'] ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </form>

        <form method="POST" action="test_allocation.php" style="width: 100%;">
          <?php if ($selected_eid): ?>
            <input type="hidden" name="eid" value="<?= htmlspecialchars($selected_eid) ?>">
          <?php endif; ?>
          <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
            <label style="color:#ccc; font-size:14px; white-space:nowrap;">Max Duties for Associates:</label>
            <input type="number" name="max_cap" value="2" min="1"
              style="background:#333; color:white; border:1px solid #444; border-radius:6px; padding:8px 10px; width:80px; outline:none;">
          </div>
          <button type="submit" name="generate_allocation" <?= !$selected_eid ? 'disabled' : '' ?>
            style="width: 100%; background-color: <?= $selected_eid ? '#18C088' : '#555' ?>; color: <?= $selected_eid ? 'black' : '#999' ?>; padding: 16px 24px; border-radius: 8px; font-weight: bold; font-size: 18px; text-align: center; cursor: <?= $selected_eid ? 'pointer' : 'not-allowed' ?>; border: none; display: flex; align-items: center; justify-content: center; gap: 12px; box-shadow: <?= $selected_eid ? '0px 4px 15px rgba(24,192,136,0.4)' : 'none' ?>;"
            <?= $selected_eid ? 'onmouseover="this.style.backgroundColor=\'#10855E\'" onmouseout="this.style.backgroundColor=\'#18C088\'"' : '' ?>>
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            GENERATE &amp; DOWNLOAD DUTY MATRIX
          </button>
        </form>

        <form method="POST" action="test_allocation.php"
          onsubmit="return confirm('Are you sure you want to completely RESET the global odometer? This physically drops all duties to 0.');">
          <button type="submit" name="reset_duty" style="color:white; background-color:#c0392b; border:none; padding:8px 14px; border-radius:5px; cursor:pointer;">Reset Total_duty(0)</button>
        </form>

      </div>
    </section>
  </main>

  <script type="module" src="./scripts/app.js"></script>
</body>

</html>
