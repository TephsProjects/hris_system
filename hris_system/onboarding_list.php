<?php
session_start();
include('includes/db.php');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Fetch all employees for the filter
$employees = $conn->query("SELECT emp_id, CONCAT(first_name, ' ', last_name) AS full_name FROM employees ORDER BY first_name ASC");

// Build WHERE condition if filter is applied
$where = "";
if (isset($_GET['emp_ids']) && !empty($_GET['emp_ids'])) {
    $emp_ids = array_map('intval', $_GET['emp_ids']);
    $where = "WHERE o.emp_id IN (" . implode(',', $emp_ids) . ")";
}

// Fetch onboarding tasks (with employee name)
$sql = "
    SELECT o.*, CONCAT(e.first_name, ' ', e.last_name) AS employee_name
    FROM onboarding_tasks o
    LEFT JOIN employees e ON o.emp_id = e.emp_id
    $where
    ORDER BY o.due_date ASC
";
$tasks = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Onboarding Tasks | HRIS</title>
<link rel="stylesheet" href="assets/css/style.css">
<style>
    .status-select {
        padding: 6px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    .pending { color: #b36b00; font-weight: bold; }
    .completed { color: green; font-weight: bold; }

    .due-soon { background: #fff3cd; } /* yellow */
    .overdue { background: #f8d7da; }  /* red */
    .done { background: #d4edda; }     /* green */

    .filter-box {
        margin-bottom: 15px;
        background: #f1f1f1;
        padding: 10px;
        border-radius: 8px;
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
    /* Filter Card */
.filter-card {
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    max-width: 400px;
    margin-bottom: 25px;
}
.filter-card h4 {
    margin-bottom: 15px;
    color: #007bff;
    font-weight: 600;
}
.filter-card select {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 14px;
    min-height: 120px;
}
.filter-buttons {
    margin-top: 15px;
    display: flex;
    gap: 10px;
}
.filter-buttons button {
    flex: 1;
    padding: 10px;
    border: none;
    border-radius: 8px;
    background-color: #007bff;
    color: #fff;
    font-weight: 600;
    cursor: pointer;
}
.filter-buttons button:hover {
    background-color: #0056b3;
}
.filter-buttons .btn-clear {
    flex: 1;
    text-align: center;
    background-color: #6c757d;
    color: #fff;
    padding: 10px;
    border-radius: 8px;
    text-decoration: none;
}
.filter-buttons .btn-clear:hover {
    background-color: #5a6268;
}

/* Onboarding Table */
.onboarding-table {
    width: 100%;
    border-collapse: collapse;
}
.onboarding-table th, .onboarding-table td {
    padding: 12px 15px;
    border-bottom: 1px solid #e0e0e0;
    text-align: left;
}
.onboarding-table tr:hover {
    background-color: #f8f9fa;
}
.status-select {
    padding: 6px;
    border-radius: 6px;
    border: 1px solid #ccc;
}

/* Status row colors */
.done { background-color: #d4edda; }
.overdue { background-color: #f8d7da; }
.due-soon { background-color: #fff3cd; }

/* Messages */
#msg {
    margin: 10px 0;
    padding: 12px;
    font-weight: bold;
    display: none;
    border-radius: 8px;
    text-align: center;
}
#msg.success { background-color: #d4edda; color: #155724; }
#msg.error { background-color: #f8d7da; color: #721c24; }

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
        <!-- Separator -->
            <hr class="sidebar-separator">

            <li class="sidebar-section-title">Onboarding & Training</li>
            
            <li class="dropdown">
                <a href="#" class="dropdown-btn" onclick="toggleDropdown(event)">
                    Onboarding & Training ▼
                </a>
                <ul class="dropdown-content">
                    <li><a href="onboarding_list.php" class="active">Onboarding Tasks</a></li>
                    <li><a href="add_onboarding.php">Add Onboarding Task</a></li>
                    <li><a href="training_list.php">Trainings</a></li>
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
    <h3>Onboarding Tasks</h3>
    <div id="msg"></div>

    <!-- Filter Box Card -->
    <div class="filter-card">
        <h4>Filter by Employee</h4>
        <form method="GET" id="filterForm">
            <select name="emp_ids[]" multiple>
                <?php while ($emp = $employees->fetch_assoc()): ?>
                    <option value="<?= $emp['emp_id']; ?>"
                        <?= (isset($emp_ids) && in_array($emp['emp_id'], $emp_ids)) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($emp['full_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <div class="filter-buttons">
                <button type="submit">Apply Filter</button>
                <a href="onboarding_list.php" class="btn-clear">Clear</a>
            </div>
        </form>
    </div>

    <!-- Onboarding Table -->
    <table class="onboarding-table">
        <thead>
            <tr>
                <th>Employee</th>
                <th>Task</th>
                <th>Due Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($task = $tasks->fetch_assoc()): 
            $due_date = $task['due_date'];
            $today = date('Y-m-d');
            $row_class = '';

            if ($task['status'] === 'Completed') $row_class = 'done';
            elseif ($due_date < $today) $row_class = 'overdue';
            elseif ($due_date === date('Y-m-d', strtotime('+1 day')) || $due_date === $today) $row_class = 'due-soon';
        ?>
            <tr class="<?= $row_class; ?>">
                <td><?= htmlspecialchars($task['employee_name']); ?></td>
                <td><?= htmlspecialchars($task['task_name']); ?></td>
                <td><?= htmlspecialchars($task['due_date']); ?></td>
                <td>
                    <select class="status-select" onchange="updateTaskStatus(<?= $task['task_id']; ?>, this.value)">
                        <option value="Pending" <?= $task['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="Completed" <?= $task['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                    </select>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
function toggleDropdown(e) {
    e.preventDefault();
    e.target.closest('.dropdown').classList.toggle('active');
}

// Update task status via AJAX
function updateTaskStatus(taskId, newStatus) {
    fetch('update_task_status.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'task_id=' + taskId + '&status=' + encodeURIComponent(newStatus)
    })
    .then(r => r.text())
    .then(data => {
        const msg = document.getElementById('msg');
        msg.style.display = 'block';
        if (data === 'success') {
            msg.className = 'success';
            msg.textContent = 'Task status updated!';
            setTimeout(() => location.reload(), 800);
        } else {
            msg.className = 'error';
            msg.textContent = 'Error updating task status.';
        }
        setTimeout(() => msg.style.display = 'none', 2000);
    });
}
</script>
</body>
</html>
