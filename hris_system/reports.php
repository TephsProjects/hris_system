<?php
session_start();
include('includes/db.php');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Handle filters
$report_type = isset($_GET['report_type']) ? $_GET['report_type'] : 'overview';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Build SQL based on report type
$where_clause = '';
if (!empty($date_from) && !empty($date_to)) {
    $where_clause = "WHERE date_hired BETWEEN '$date_from' AND '$date_to'";
}

switch ($report_type) {
    case 'department':
        $sql = "SELECT department AS label, COUNT(*) AS total FROM employees $where_clause GROUP BY department ORDER BY total DESC";
        break;
    case 'gender':
        $sql = "SELECT gender AS label, COUNT(*) AS total FROM employees $where_clause GROUP BY gender";
        break;
    case 'civil_status':
        $sql = "SELECT civil_status AS label, COUNT(*) AS total FROM employees $where_clause GROUP BY civil_status";
        break;
    default:
        $sql = "SELECT COUNT(*) AS total_employees FROM employees $where_clause";
        break;
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports | HRIS</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/reports.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <h2>HR Information System</h2>
        <div class="user-info">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <ul>
            <li class="sidebar-section-title1">Tools</li>
            <li><a href="dashboard.php">Dashboard</a></li>

            <!-- Collapsible Leave Applications -->
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
                    <li><a href="benefits_list.php">Benefits List</a></li>
                </ul>
            </li>

            <li class="dropdown">
                <a href="#" class="dropdown-btn" onclick="toggleDropdown(event)">
                    Reports & Accounts ▼
                </a>
                <ul class="dropdown-content">
                    <li><a href="reports.php" class="active">Reports</a></li>
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
        <h3>HR Reports</h3>

        <!-- Filters -->
        <form method="GET" class="filter-form">
            <label for="report_type"><strong>Report Type:</strong></label>
            <select name="report_type" id="report_type">
                <option value="overview" <?= $report_type == 'overview' ? 'selected' : '' ?>>Employee Overview</option>
                <option value="department" <?= $report_type == 'department' ? 'selected' : '' ?>>By Department</option>
                <option value="gender" <?= $report_type == 'gender' ? 'selected' : '' ?>>By Gender</option>
                <option value="civil_status" <?= $report_type == 'civil_status' ? 'selected' : '' ?>>By Civil Status</option>
            </select>

            <label><strong>From:</strong></label>
            <input type="date" name="date_from" value="<?= htmlspecialchars($date_from) ?>">

            <label><strong>To:</strong></label>
            <input type="date" name="date_to" value="<?= htmlspecialchars($date_to) ?>">

            <button type="submit" class="btn apply-btn">Apply</button>
            <a href="reports.php" class="btn reset-btn">Reset</a>
        </form>

        <!-- Reports -->
        <div class="report-container">
            <?php if ($report_type == 'overview'): ?>
                <?php $row = $result->fetch_assoc(); ?>
                <div class="report-card" style="text-align:center;">
                    <h4>Total Employees</h4>
                    <p style="font-size:2rem; color:#007bff;"><?php echo htmlspecialchars($row['total_employees']); ?></p>
                </div>
            <?php else: ?>
                <table id="reportTable">
                    <thead>
                        <tr>
                            <th><?php echo ucfirst(str_replace('_', ' ', $report_type)); ?></th>
                            <th>Total Employees</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $labels = [];
                        $values = [];
                        while ($row = $result->fetch_assoc()):
                            $labels[] = $row['label'];
                            $values[] = $row['total'];
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['label']); ?></td>
                            <td><?php echo htmlspecialchars($row['total']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <div class="chart-container">
                    <canvas id="reportChart"></canvas>
                </div>

                <div class="export-btns">
                    <button type="button" onclick="exportToExcel()">Export to Excel</button>
                    <button type="button" onclick="exportToPDF()">Export to PDF</button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        <?php if ($report_type != 'overview'): ?>
        const labels = <?php echo json_encode($labels); ?>;
        const data = <?php echo json_encode($values); ?>;

        const ctx = document.getElementById('reportChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Employees',
                    data: data,
                    backgroundColor: 'rgba(0, 123, 255, 0.6)',
                    borderColor: '#007bff',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
        <?php endif; ?>

        // Export to Excel
        function exportToExcel() {
            const table = document.getElementById("reportTable").outerHTML;
            const blob = new Blob([table], { type: "application/vnd.ms-excel" });
            const link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.download = "HR_Report.xls";
            link.click();
        }

        // Export to PDF
        function exportToPDF() {
            const printWindow = window.open('', '', 'height=700,width=900');
            printWindow.document.write('<html><head><title>HR Report</title></head><body>');
            printWindow.document.write('<h3>HR Report - <?php echo ucfirst($report_type); ?></h3>');
            printWindow.document.write(document.getElementById("reportTable").outerHTML);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        }
    </script>
        <script>
function toggleDropdown(event) {
    event.preventDefault();
    const parent = event.target.closest('.dropdown');
    parent.classList.toggle('active');
}
</script>
</body>
</html>
