<?php
session_start();
include 'includes/db.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM employees WHERE emp_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Employee not found.";
    exit();
}

$emp = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Details | HRIS</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/employee_view.css">
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
        <div class="employee-profile">
            <div class="employee-header">
                <img src="<?php echo !empty($emp['image']) ? htmlspecialchars($emp['image']) : 'assets/images/default.png'; ?>" alt="Employee Image">
                <div class="info">
                    <h2><?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?></h2>
                    <p><strong>Employee No:</strong> <?php echo htmlspecialchars($emp['employee_no']); ?></p>
                    <p><strong><?php echo htmlspecialchars($emp['position']); ?></strong> — <?php echo htmlspecialchars($emp['department']); ?></p>
                    <p>Hired: <?php echo htmlspecialchars($emp['date_hired']); ?></p>
                </div>
            </div>

            <div class="employee-body">
                <div class="info-section">
                    <h3>Personal Information</h3>
                    <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($emp['date_of_birth']); ?></p>
                    <p><strong>Gender:</strong> <?php echo htmlspecialchars($emp['gender']); ?></p>
                    <p><strong>Civil Status:</strong> <?php echo htmlspecialchars($emp['civil_status']); ?></p>
                    <p><strong>Nationality:</strong> <?php echo htmlspecialchars($emp['nationality']); ?></p>
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($emp['address']); ?></p>
                </div>

                <div class="info-section">
                    <h3>Contact Information</h3>
                    <p><strong>Mobile:</strong> <?php echo htmlspecialchars($emp['mobile_number']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($emp['email']); ?></p>
                    <p><strong>Contact Person:</strong> <?php echo htmlspecialchars($emp['contact_person']); ?></p>
                    <p><strong>Emergency Contact:</strong> <?php echo htmlspecialchars($emp['emergency_contact']); ?></p>
                </div>

                <div class="info-section">
                    <h3>Employment Details</h3>
                    <p><strong>Department:</strong> <?php echo htmlspecialchars($emp['department']); ?></p>
                    <p><strong>Position:</strong> <?php echo htmlspecialchars($emp['position']); ?></p>
                    <p><strong>Tax Status:</strong> <?php echo htmlspecialchars($emp['tax_status']); ?></p>
                    <p><strong>Date Hired:</strong> <?php echo htmlspecialchars($emp['date_hired']); ?></p>
                    <p><strong>Contract End Date:</strong> 
                        <?php 
                            if (!empty($emp['contract_end_date'])) {
                                echo htmlspecialchars($emp['contract_end_date']);
                                // 🔔 Alert visually if contract is about to expire (within 30 days)
                                $today = new DateTime();
                                $end = new DateTime($emp['contract_end_date']);
                                $diff = $today->diff($end)->days;
                                if ($end < $today) {
                                    echo " <span style='color:red;'>(Expired)</span>";
                                } elseif ($diff <= 30) {
                                    echo " <span style='color:orange;'>(Expiring soon)</span>";
                                }
                            } else {
                                echo "<span style='color:gray;'>No contract end date</span>";
                            }
                        ?>
                    </p>
                    <p><strong>Contract File:</strong> 
                        <?php if (!empty($emp['contract_file'])): ?>
                            <a href="<?php echo htmlspecialchars($emp['contract_file']); ?>" target="_blank" style="color:#007bff;">📄 View Contract</a>
                        <?php else: ?>
                            <span style="color:gray;">No file uploaded</span>
                        <?php endif; ?>
                    </p>
                </div>

                <div class="info-section">
                    <h3>Government IDs</h3>
                    <p><strong>TIN No.:</strong> <?php echo htmlspecialchars($emp['tin_no']); ?></p>
                    <p><strong>SSS No.:</strong> <?php echo htmlspecialchars($emp['sss_no']); ?></p>
                    <p><strong>PhilHealth No.:</strong> <?php echo htmlspecialchars($emp['philhealth_no']); ?></p>
                </div>
            </div>

            <div class="footer-actions">
                <a href="edit_employee.php?id=<?php echo $emp['emp_id']; ?>" class="back-btn" style="background:#28a745;">✏️ Edit</a>
                <a href="delete_employee.php?id=<?php echo $emp['emp_id']; ?>" 
                class="back-btn" 
                style="background:#dc3545;"
                onclick="return confirm('Are you sure you want to delete this employee?');">🗑 Delete</a>
                <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
            </div>
        </div>
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
