<?php
session_start();
include('includes/db.php');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Fetch employees for the filter
$employees = $conn->query("SELECT emp_id, CONCAT(first_name, ' ', last_name) AS full_name FROM employees ORDER BY first_name ASC");

// Build WHERE clause if filter applied
$where = "";
if (isset($_GET['emp_ids']) && !empty($_GET['emp_ids'])) {
    $emp_ids = array_map('intval', $_GET['emp_ids']);
    $where = "WHERE t.emp_id IN (" . implode(',', $emp_ids) . ")";
}

// Fetch trainings
$sql = "
    SELECT t.*, CONCAT(e.first_name, ' ', e.last_name) AS employee_name
    FROM trainings t
    LEFT JOIN employees e ON t.emp_id = e.emp_id
    $where
    ORDER BY t.completion_date ASC, t.progress DESC
";
$trainings = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Training List | HRIS</title>
<link rel="stylesheet" href="assets/css/style.css">
<style>
    .progress-bar {
        width: 100%;
        background: #eee;
        border-radius: 10px;
        height: 20px;
        position: relative;
    }
    .progress-fill {
        height: 100%;
        border-radius: 10px;
        text-align: center;
        color: white;
        font-size: 12px;
        line-height: 20px;
    }
    .progress-low { background: #dc3545; }
    .progress-mid { background: #ffc107; }
    .progress-high { background: #28a745; }

    .completed-row { background: #d4edda; }

    .filter-box {
        margin-bottom: 15px;
        background: #f1f1f1;
        padding: 10px;
        border-radius: 8px;
    }

    .progress-input {
        width: 60px;
        text-align: center;
        padding: 5px;
    }

    #msg {
        margin: 10px 0;
        padding: 10px;
        font-weight: bold;
        display: none;
        border-radius: 5px;
    }
    #msg.success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    #msg.error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
</style>
</head>
<body>
<div class="navbar">
    <h2>HR Information System</h2>
    <div class="user-info">
        <span>Welcome, <?= $_SESSION['full_name']; ?></span>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<div class="sidebar">
    <ul>
        <li class="sidebar-section-title1">Tools</li>
        <li><a href="dashboard.php">Dashboard</a></li>
    
        <li class="dropdown">
            <a href="#" class="dropdown-btn" onclick="toggleDropdown(event)">Leave Applications ▼</a>
            <ul class="dropdown-content">
                <li><a href="add_leave.php">Leave Form</a></li>
                <li><a href="leave_requests.php">Leave Request Lists</a></li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#" class="dropdown-btn" onclick="toggleDropdown(event)">Payroll ▼</a>
            <ul class="dropdown-content">
                <li><a href="add_payroll.php">Add Payroll</a></li>
                <li><a href="payroll.php">Payroll List</a></li>
                <li><a href="add_benefits.php">Add Benefits</a></li>
                <li><a href="benefits_list.php">Benefits List</a></li>
            </ul>
        </li>
        <li class="dropdown">
                <a href="#" class="dropdown-btn" onclick="toggleDropdown(event)">
                    Reports & Accounts ▼
                </a>
                <ul class="dropdown-content">
                    <li><a href="reports.php">Reports</a></li>
                    <li><a href="accounts.php">Accounts</a></li>
                    <li><a href="add_employee.php">Add Employee</a></li>
                </ul>
            </li>

        <!-- Separator -->
            <hr class="sidebar-separator">

            <!-- Recruitment Section -->
            <li class="sidebar-section-title">Recruitments and Hiring</li>

            <li class="dropdown">
                <a href="#" class="dropdown-btn" onclick="toggleDropdown(event)">
                    Recruitment ▼
                </a>
                <ul class="dropdown-content">
                    <li><a href="add_job.php">Add Job Opening</a></li>
                    <li><a href="job_list.php">Job Openings</a></li>
                    <li><a href="add_candidate.php">Add Candidate</a></li>
                    <li><a href="candidate_list.php">Candidates</a></li>
                </ul>
            </li>

            <hr class="sidebar-separator">

            <li class="sidebar-section-title">Utilities</li>

            <li class="dropdown">
                <a href="#" class="dropdown-btn" onclick="toggleDropdown(event)">
                    Branch / Location ▼
                </a>
                <ul class="dropdown-content">
                    <li><a href="add_branch.php">Add Branch / Location</a></li>
                    <li><a href="branch_list.php">Branch / Location List</a></li>
                </ul>
            </li>

            <hr class="sidebar-separator">

            <li class="sidebar-section-title">Onboarding & Training</li>
            
            <li class="dropdown">
                <a href="#" class="dropdown-btn" onclick="toggleDropdown(event)">
                    Onboarding & Training ▼
                </a>
                <ul class="dropdown-content">
                    <li><a href="onboarding_list.php">Onboarding Tasks</a></li>
                    <li><a href="add_onboarding.php">Add Onboarding Task</a></li>
                    <li><a href="training_list.php" class="active">Trainings</a></li>
                    <li><a href="add_training.php">Add Training</a></li>
                </ul>
            </li>

        <hr class="sidebar-separator">
            
            <li class="sidebar-section-title">Performance Management</li>

            <!-- Performance Section -->
             <li class="dropdown">
                <a href="#" class="dropdown-btn" onclick="toggleDropdown(event)">
                    Evaluation ▼
                </a>
                <ul class="dropdown-content">
                    <li><a href="add_performance.php">Add Evaluation</a></li>
                    <li><a href="performance_list.php">Evaluation List</a></li>
                </ul>
            </li>
    </ul>
</div>

<div class="main-content">
    <h3>Training List</h3>
    <div id="msg"></div>

    <div class="filter-box">
        <form method="GET" id="filterForm">
            <label>Filter by Employee:</label><br>
            <select name="emp_ids[]" multiple size="5" style="width: 250px;">
                <?php while ($emp = $employees->fetch_assoc()): ?>
                    <option value="<?= $emp['emp_id']; ?>"
                        <?= (isset($emp_ids) && in_array($emp['emp_id'], $emp_ids)) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($emp['full_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <br><br>
            <button type="submit">Apply Filter</button>
            <a href="training_list.php" class="btn-clear">Clear</a>
        </form>
    </div>

    <table>
        <tr>
            <th>Employee</th>
            <th>Training Title</th>
            <th>Progress</th>
            <th>Completion Date</th>
            <th>Action</th>
        </tr>
        <?php while ($t = $trainings->fetch_assoc()): 
            $fillClass = 'progress-low';
            if ($t['progress'] >= 70) $fillClass = 'progress-high';
            elseif ($t['progress'] >= 40) $fillClass = 'progress-mid';

            $rowClass = ($t['progress'] >= 100) ? 'completed-row' : '';
        ?>
        <tr class="<?= $rowClass; ?>">
            <td><?= htmlspecialchars($t['employee_name']); ?></td>
            <td><?= htmlspecialchars($t['title']); ?></td>
            <td>
                <div class="progress-bar">
                    <div class="progress-fill <?= $fillClass; ?>" style="width: <?= $t['progress']; ?>%;">
                        <?= $t['progress']; ?>%
                    </div>
                </div>
            </td>
            <td><?= htmlspecialchars($t['completion_date'] ?? '—'); ?></td>
            <td>
                <input type="number" class="progress-input" min="0" max="100"
                       value="<?= $t['progress']; ?>" id="progress_<?= $t['training_id']; ?>">
                <button onclick="updateProgress(<?= $t['training_id']; ?>)">Update</button>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<script>
function toggleDropdown(e) {
    e.preventDefault();
    e.target.closest('.dropdown').classList.toggle('active');
}

// AJAX update progress
function updateProgress(trainingId) {
    const progress = document.getElementById('progress_' + trainingId).value;
    fetch('update_training_progress.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'training_id=' + trainingId + '&progress=' + progress
    })
    .then(r => r.text())
    .then(data => {
        const msg = document.getElementById('msg');
        msg.style.display = 'block';
        if (data === 'success') {
            msg.className = 'success';
            msg.textContent = 'Progress updated successfully!';
            setTimeout(() => location.reload(), 800);
        } else {
            msg.className = 'error';
            msg.textContent = 'Error updating progress.';
        }
        setTimeout(() => msg.style.display = 'none', 2000);
    });
}
</script>
</body>
</html>
