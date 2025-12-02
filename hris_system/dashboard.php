<?php
session_start();
include('includes/db.php');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Initialize filters
$search = '';
$date_from = '';
$date_to = '';

// Base query
$query = "SELECT * FROM employees WHERE 1=1";
$params = [];
$types = "";

// Search filter
if (isset($_GET['search']) && $_GET['search'] !== '') {
    $search = $_GET['search'];
    $search_query = "%" . $search . "%";
    $query .= " AND (first_name LIKE ? 
                OR last_name LIKE ? 
                OR department LIKE ? 
                OR position LIKE ? 
                OR civil_status LIKE ? 
                OR gender LIKE ? 
                OR mobile_number LIKE ? 
                OR email LIKE ?)";
    $types .= "ssssssss";
    array_push($params, $search_query, $search_query, $search_query, $search_query, $search_query, $search_query, $search_query, $search_query);
}

// Date hired range filter
if (!empty($_GET['date_from']) && !empty($_GET['date_to'])) {
    $date_from = $_GET['date_from'];
    $date_to = $_GET['date_to'];
    $query .= " AND date_hired BETWEEN ? AND ?";
    $types .= "ss";
    array_push($params, $date_from, $date_to);
} elseif (!empty($_GET['date_from'])) {
    $date_from = $_GET['date_from'];
    $query .= " AND date_hired >= ?";
    $types .= "s";
    array_push($params, $date_from);
} elseif (!empty($_GET['date_to'])) {
    $date_to = $_GET['date_to'];
    $query .= " AND date_hired <= ?";
    $types .= "s";
    array_push($params, $date_to);
}

// Prepare and execute
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$employees = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HRIS Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet" />

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <style>
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

.filter-form input {
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
            <!-- Performance Section -->
            <li class="sidebar-section-title1">Tools</li>
            <li><a href="dashboard.php" class="active">Dashboard</a></li>

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

            <!-- Collapsible Payroll -->
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

            <!-- Onboarding & Training Section -->
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
        <h3>Employee List</h3>

        <div class="dashboard-grid">
            <!-- Left Column: Employee Table -->
            <div class="employee-list">
                <!-- Filters -->
                <div class="controls-container">
                    <!-- Filters -->
                    <form method="GET" action="dashboard.php" class="filter-form">
                        <input type="text" name="search" placeholder="Search employee..." value="<?= htmlspecialchars($search) ?>">
                        <input type="date" name="date_from" value="<?= htmlspecialchars($date_from) ?>">
                        <input type="date" name="date_to" value="<?= htmlspecialchars($date_to) ?>">
                        <button type="submit" class="btn filter-btn">🔍 Search</button>
                        <?php if ($search || $date_from || $date_to): ?>
                            <a href="dashboard.php" class="btn clear-btn">❌ Clear</a>
                        <?php endif; ?>
                    </form>

                    <!-- Export Buttons -->
                    <div class="export-buttons">
                        <button onclick="exportCSV()" class="btn export-btn csv-btn">📄 Export CSV</button>
                        <button onclick="exportExcel()" class="btn export-btn excel-btn">📊 Export Excel</button>
                    </div>
                </div>

                <!-- Visible Table -->
                <div class="table-container">
                    <table id="visibleTable">
                        <thead>
                            <tr>
                                <th>Employee No</th>
                                <th>Full Name</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>Civil Status</th>
                                <th>Gender</th>
                                <th>Date Hired</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($employees as $row): ?>
                            <tr onclick="window.location='employee_view.php?id=<?php echo $row['emp_id']; ?>'" style="cursor:pointer;">
                                <td><?= htmlspecialchars($row['employee_no']) ?></td>
                                <td><?= htmlspecialchars($row['first_name'].' '.$row['last_name']) ?></td>
                                <td><?= htmlspecialchars($row['department']) ?></td>
                                <td><?= htmlspecialchars($row['position']) ?></td>
                                <td><?= htmlspecialchars($row['civil_status']) ?></td>
                                <td><?= htmlspecialchars($row['gender']) ?></td>
                                <td><?= htmlspecialchars($row['date_hired']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right Column: Calendar -->
            <div class="dashboard-calendar">
                <h2>Calendar</h2>
                <div id="calendar"></div>
            </div>
        </div>

        <!-- Hidden Full Export Table -->
        <table id="exportTable">
            <thead>
                <tr>
                    <th>Employee No</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Address</th>
                    <th>Age</th>
                    <th>Date of Birth</th>
                    <th>Department</th>
                    <th>Position</th>
                    <th>Civil Status</th>
                    <th>Tax Status</th>
                    <th>TIN</th>
                    <th>SSS</th>
                    <th>PhilHealth</th>
                    <th>Nationality</th>
                    <th>Gender</th>
                    <th>Mobile</th>
                    <th>Email</th>
                    <th>Contact Person</th>
                    <th>Emergency Contact No</th>
                    <th>Date Hired</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($employees as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['employee_no']) ?></td>
                    <td><?= htmlspecialchars($row['first_name']) ?></td>
                    <td><?= htmlspecialchars($row['last_name']) ?></td>
                    <td><?= htmlspecialchars($row['address']) ?></td>
                    <td><?= htmlspecialchars($row['age']) ?></td>
                    <td><?= htmlspecialchars($row['date_of_birth']) ?></td>
                    <td><?= htmlspecialchars($row['department']) ?></td>
                    <td><?= htmlspecialchars($row['position']) ?></td>
                    <td><?= htmlspecialchars($row['civil_status']) ?></td>
                    <td><?= htmlspecialchars($row['tax_status']) ?></td>
                    <td><?= htmlspecialchars($row['tin_no']) ?></td>
                    <td><?= htmlspecialchars($row['sss_no']) ?></td>
                    <td><?= htmlspecialchars($row['philhealth_no']) ?></td>
                    <td><?= htmlspecialchars($row['nationality']) ?></td>
                    <td><?= htmlspecialchars($row['gender']) ?></td>
                    <td><?= htmlspecialchars($row['mobile_number']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['contact_person']) ?></td>
                    <td><?= htmlspecialchars($row['emergency_contact']) ?></td>
                    <td><?= htmlspecialchars($row['date_hired']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Export Scripts -->
    <script>
        function exportCSV() {
            const rows = document.querySelectorAll("#exportTable tr");
            const csv = Array.from(rows).map(row =>
                Array.from(row.querySelectorAll("th, td"))
                    .map(cell => `"${cell.innerText.replace(/"/g, '""')}"`)
                    .join(",")
            ).join("\n");
            const blob = new Blob([csv], { type: 'text/csv' });
            const link = document.createElement('a');
            link.download = 'employees.csv';
            link.href = URL.createObjectURL(blob);
            link.click();
        }

        function exportExcel() {
            const tableHTML = document.getElementById("exportTable").outerHTML.replace(/ /g, '%20');
            const link = document.createElement("a");
            link.href = 'data:application/vnd.ms-excel,' + tableHTML;
            link.download = 'employees.xls';
            link.click();
        }
    </script>
<script>
function toggleDropdown(event) {
    event.preventDefault();
    const parent = event.target.closest('.dropdown');
    parent.classList.toggle('active');
}

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',  // Month view
        height: 'auto',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },
        selectable: true,
        dateClick: function(info) {
            alert('Date clicked: ' + info.dateStr);
        }
    });

    calendar.render();
});
</script>
</body>
</html>