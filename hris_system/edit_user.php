<?php
session_start();
include('includes/db.php');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: accounts.php");
    exit();
}

$user_id = intval($_GET['id']);

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found!";
    exit();
}

// Update user info
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $role = $_POST['role'];

    // Password change (optional)
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $update_stmt = $conn->prepare("UPDATE users SET full_name=?, username=?, password=?, role=? WHERE id=?");
        $update_stmt->bind_param("ssssi", $full_name, $username, $password, $role, $user_id);
    } else {
        $update_stmt = $conn->prepare("UPDATE users SET full_name=?, username=?, role=? WHERE id=?");
        $update_stmt->bind_param("sssi", $full_name, $username, $role, $user_id);
    }

    if ($update_stmt->execute()) {
        $success = "Account updated successfully!";
        // Refresh user info after update
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
    } else {
        $error = "Error updating account: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Account | HRIS</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/edit_user.css">
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
                    <li><a href="reports.php">Reports</a></li>
                    <li><a href="accounts.php" class="active">Accounts</a></li>
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
        <h3>Edit Account</h3>

        <?php if (isset($success)): ?>
            <div class="message success"><?php echo $success; ?></div>
        <?php elseif (isset($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="edit-account-form">
            <form method="POST" action="">
                <label>Full Name</label>
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>

                <label>Username</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

                <label>New Password (optional)</label>
                <input type="password" name="password" placeholder="Leave blank to keep current password">

                <label>Role</label>
                <select name="role" required>
                    <option value="Admin" <?php if ($user['role'] == 'Admin') echo 'selected'; ?>>Admin</option>
                    <option value="HR Staff" <?php if ($user['role'] == 'HR Staff') echo 'selected'; ?>>HR Staff</option>
                </select>

                <button type="submit">Update Account</button>
                <a href="accounts.php" class="btn-clear" style="margin-left:10px;">Back</a>
            </form>
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
