<?php
session_start();
include('includes/db.php');
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

$query = "SELECT b.*, CONCAT(e.first_name, ' ', e.last_name) AS employee_name 
          FROM benefits b 
          JOIN employees e ON b.emp_id = e.emp_id 
          ORDER BY b.date_assigned DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Benefits List | HRIS</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <h2>HR Information System</h2>
    <div class="user-info">
        <span>Welcome, <?= htmlspecialchars($_SESSION['full_name']); ?></span>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<!-- Sidebar -->
<div class="sidebar">
    <ul>
        <li class="sidebar-section-title1">Tools</li>
        <li><a href="dashboard.php">Dashboard</a></li>

        <li class="dropdown">
            <a href="#" class="dropdown-btn" onclick="toggleDropdown(event)">
                Leave Applications ▼
            </a>
            <ul class="dropdown-content">
                <li><a href="add_leave.php">Leave Form</a></li>
                <li><a href="leave_requests.php">Leave Request Lists</a></li>
            </ul>
        </li>

        <li class="dropdown">
            <a href="#" class="dropdown-btn" onclick="toggleDropdown(event)">
                Payroll ▼
            </a>
            <ul class="dropdown-content">
                <li><a href="add_payroll.php">Add Payroll</a></li>
                <li><a href="payroll.php">Payroll List</a></li>
                <li><a href="add_benefits.php">Add Benefits</a></li>
                <li><a href="benefits_list.php" class="active">Benefits List</a></li>
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

        <hr class="sidebar-separator">

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
        
        <li class="sidebar-section-title">Onboarding & Training</li>
        
        <li class="dropdown">
            <a href="#" class="dropdown-btn" onclick="toggleDropdown(event)">
                Onboarding & Training ▼
            </a>
            <ul class="dropdown-content">
                <li><a href="onboarding_list.php">Onboarding Tasks</a></li>
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

<!-- Main Content -->
<div class="main-content">
    <h3>Employee Benefits</h3>
    <table class="styled-table">
        <thead>
            <tr>
                <th>Employee</th>
                <th>Health Plan</th>
                <th>Retirement Plan</th>
                <th>Insurance</th>
                <th>Dependents</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['employee_name']) ?></td>
                <td><?= htmlspecialchars($row['health_plan']) ?></td>
                <td><?= htmlspecialchars($row['retirement_plan']) ?></td>
                <td><?= htmlspecialchars($row['insurance_type']) ?></td>
                <td><?= htmlspecialchars($row['dependents']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
function toggleDropdown(event) {
    event.preventDefault();
    const parent = event.target.closest('.dropdown');
    parent.classList.toggle('active');
}
</script>
</body>
</html>
