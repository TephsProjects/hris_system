<?php
session_start();
include('includes/db.php');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Fetch leave requests with employee names
$query = "SELECT l.*, CONCAT(e.first_name, ' ', e.last_name) AS full_name, e.employee_no
          FROM leaves l
          INNER JOIN employees e ON l.emp_id = e.emp_id
          ORDER BY l.created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leave Requests | HRIS</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .main-content {
            padding: 30px;
        }

        h3 {
            text-align: center;
            color: #007bff;
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 14px 16px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            color: #000000ff;
            text-transform: uppercase;
            font-size: 14px;
        }

        tr:hover {
            background: #f9f9f9;
        }

        select.status-select {
            padding: 6px 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            background: #f8f9fa;
            font-weight: 500;
            cursor: pointer;
        }

        select.status-select.approved {
            background: #d4edda;
            color: #155724;
        }

        select.status-select.denied {
            background: #f8d7da;
            color: #721c24;
        }

        select.status-select.pending {
            background: #fff3cd;
            color: #856404;
        }

        select.status-select:hover {
            opacity: 0.9;
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
            <li class="sidebar-section-title1">Tools</li>
            <li><a href="dashboard.php">Dashboard</a></li>

            <!-- Collapsible Leave Applications -->
            <li class="dropdown">
                <a href="#" class="dropdown-btn" onclick="toggleDropdown(event)" class="active">
                    Leave Applications ▼
                </a>
                <ul class="dropdown-content">
                    <li><a href="add_leave.php">Leave Form</a></li>
                    <li><a href="leave_requests.php" class="active">Leave Request Lists</a></li>
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
        <h3>Employee Leave Requests</h3>

        <table>
            <thead>
                <tr>
                    <th>Employee No</th>
                    <th>Employee Name</th>
                    <th>Leave Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Reason</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="leaveTable">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr id="leave-<?php echo $row['leave_id']; ?>">
                        <td><?php echo htmlspecialchars($row['employee_no']); ?></td>
                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['leave_type']); ?></td>
                        <td><?php echo htmlspecialchars($row['start_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['end_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['reason']); ?></td>
                        <td>
                            <select 
                                class="status-select <?php echo strtolower($row['status']); ?>" 
                                onchange="updateLeaveStatus(<?php echo $row['leave_id']; ?>, this)">
                                <option value="Pending" <?php echo $row['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="Approved" <?php echo $row['status'] === 'Approved' ? 'selected' : ''; ?>>Approved</option>
                                <option value="Denied" <?php echo $row['status'] === 'Denied' ? 'selected' : ''; ?>>Denied</option>
                            </select>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Update leave status when dropdown changes
        function updateLeaveStatus(leaveId, selectElem) {
            const newStatus = selectElem.value;
            selectElem.className = "status-select " + newStatus.toLowerCase();

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "update_leave_status.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        const res = JSON.parse(xhr.responseText);
                        if (!res.success) {
                            alert("Error: " + res.message);
                        }
                    } catch (e) {
                        console.error("Invalid JSON:", xhr.responseText);
                        alert("Unexpected server error occurred.");
                    }
                }
            };
            xhr.send("leave_id=" + encodeURIComponent(leaveId) + "&status=" + encodeURIComponent(newStatus));
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
