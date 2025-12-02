<?php
session_start();
include('includes/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Filters
$search = $_GET['search'] ?? '';
$month = $_GET['month'] ?? '';
$payment_type = $_GET['payment_type'] ?? '';

// Base query
$query = "SELECT p.*, CONCAT(e.first_name, ' ', e.last_name) AS full_name, e.employee_no
          FROM payroll p 
          INNER JOIN employees e ON p.emp_id = e.emp_id
          WHERE 1=1";

$params = [];
$types = "";

// Employee name filter
if ($search) {
    $query .= " AND (e.first_name LIKE ? OR e.last_name LIKE ?)";
    $types .= "ss";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Payroll month filter
if ($month) {
    $query .= " AND p.payroll_month LIKE ?";
    $types .= "s";
    $params[] = "%$month%";
}

// Payment type filter
if ($payment_type) {
    $query .= " AND p.payment_type = ?";
    $types .= "s";
    $params[] = $payment_type;
}

$query .= " ORDER BY p.created_at DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payroll | HRIS</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .filter-container { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin-bottom: 20px; }
        .filter-container input, .filter-container select { padding: 6px; border-radius: 4px; border: 1px solid #ccc; }
        .btn { padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; color: #fff; }
        .btn-primary { background-color: #007bff; }
        .btn-secondary { background-color: #6c757d; text-decoration: none; display: inline-block; }
        .export-btn { padding: 6px 12px; margin-right: 5px; border-radius: 4px; cursor: pointer; margin-bottom: 20px; }
        .export-buttons {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.export-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    font-size: 14px;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    color: #fff;
    transition: 0.3s;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}

.csv-btn {
    background-color: #28a745; /* green for CSV */
}

.csv-btn:hover {
    background-color: #218838;
    transform: translateY(-2px);
}

.excel-btn {
    background-color: #007bff; /* blue for Excel */
}

.excel-btn:hover {
    background-color: #0056b3;
    transform: translateY(-2px);
}

/* Optional: make icons slightly larger */
.export-btn::before {
    font-size: 16px;
}
.controls-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    gap: 15px;
}

/* Filters Form */
.filter-form {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
}

.filter-form input,
.filter-form select {
    padding: 8px 12px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
}

.filter-btn {
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 8px 15px;
    cursor: pointer;
    transition: 0.3s;
}
.filter-btn:hover {
    background-color: #0056b3;
}

.clear-btn {
    background-color: #6c757d;
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 8px 15px;
    text-decoration: none;
    display: inline-block;
}
.clear-btn:hover {
    background-color: #5a6268;
}

/* Export Buttons */
.export-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.export-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    font-size: 14px;
    font-weight: 600;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    color: #fff;
    transition: 0.3s;
    box-shadow: 0 2px 5px rgba(0,0,0,0.15);
}

.csv-btn {
    background-color: #28a745;
}
.csv-btn:hover {
    background-color: #218838;
    transform: translateY(-1px);
}

.excel-btn {
    background-color: #007bff;
}
.excel-btn:hover {
    background-color: #0056b3;
    transform: translateY(-1px);
}

/* Responsive adjustments */
@media(max-width: 900px) {
    .controls-container {
        flex-direction: column;
        align-items: stretch;
    }
    .filter-form, .export-buttons {
        justify-content: flex-start;
    }
}

    </style>
</head>
<body>
<div class="navbar">
    <h2>HR Information System</h2>
    <div class="user-info">
        <span>Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?></span>
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
                <li><a href="payroll.php" class="active">Payroll List</a></li>
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
        <!-- Separator -->
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

<div class="main-content">
    <h3>Payroll List</h3>

    <!-- Filters -->
    <div class="controls-container">
        <!-- Filters -->
        <form method="GET" class="filter-form">
            <input type="text" name="search" placeholder="Search employee..." value="<?= htmlspecialchars($search) ?>">
            <input type="month" name="month" value="<?= htmlspecialchars($month) ?>">
            <select name="payment_type">
                <option value="">All Payment Types</option>
                <option value="monthly" <?= $payment_type==='monthly'?'selected':'' ?>>Monthly</option>
                <option value="biweekly" <?= $payment_type==='biweekly'?'selected':'' ?>>Bi-Weekly</option>
            </select>
            <button type="submit" class="btn filter-btn">Filter</button>
            <a href="payroll.php" class="btn clear-btn">Clear</a>
        </form>

        <!-- Export Buttons -->
        <div class="export-buttons">
            <button onclick="exportCSV()" class="btn export-btn csv-btn">📄 Export CSV</button>
            <button onclick="exportExcel()" class="btn export-btn excel-btn">📊 Export Excel</button>
        </div>
    </div>
    <!-- Payroll Table -->
    <table id="exportTable">
        <thead>
            <tr>
                <th>Employee No</th>
                <th>Employee Name</th>
                <th>Payment Type</th>
                <th>Period</th>
                <th>Basic Salary</th>
                <th>Allowances</th>
                <th>Deductions</th>
                <th>Net Salary</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['employee_no']) ?></td>
                <td><?= htmlspecialchars($row['full_name']) ?></td>
                <td><?= ucfirst($row['payment_type']) ?></td>
                <td><?= htmlspecialchars($row['payroll_month']) ?></td>
                <td style="background-color: #007bff; color: #fff;"><?= number_format($row['basic_salary'], 2) ?></td>
                <td><?= number_format($row['allowances'], 2) ?></td>
                <td><?= number_format($row['deductions'], 2) ?></td>
                <td class="net-salary" style="background-color: #1abb27ff; color: #fff;"><?= number_format($row['net_salary'], 2) ?></td>
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

function exportCSV() {
    const rows = document.querySelectorAll("#exportTable tr");
    const csv = Array.from(rows).map(row =>
        Array.from(row.querySelectorAll("th, td"))
            .map(cell => `"${cell.innerText.replace(/"/g, '""')}"`)
            .join(",")
    ).join("\n");
    const blob = new Blob([csv], { type: 'text/csv' });
    const link = document.createElement('a');
    link.download = 'payroll.csv';
    link.href = URL.createObjectURL(blob);
    link.click();
}

function exportExcel() {
    const tableHTML = document.getElementById("exportTable").outerHTML.replace(/ /g, '%20');
    const link = document.createElement("a");
    link.href = 'data:application/vnd.ms-excel,' + tableHTML;
    link.download = 'payroll.xls';
    link.click();
}
</script>
</body>
</html>
