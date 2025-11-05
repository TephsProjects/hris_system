<?php
session_start();
include('includes/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: benefits_list.php");
    exit();
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM benefits WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$benefit = $result->fetch_assoc();

if (!$benefit) {
    header("Location: benefits_list.php");
    exit();
}

$message = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $health_plan = $_POST['health_plan'] ?? '';
    $retirement_plan = $_POST['retirement_plan'] ?? '';
    $insurance_type = $_POST['insurance_type'] ?? '';
    $dependents = intval($_POST['dependents'] ?? 0);

    $update = $conn->prepare("UPDATE benefits SET health_plan=?, retirement_plan=?, insurance_type=?, dependents=? WHERE id=?");
    $update->bind_param("sssii", $health_plan, $retirement_plan, $insurance_type, $dependents, $id);

    if ($update->execute()) {
        $message = "✅ Benefits record updated successfully.";
    } else {
        $error = "❌ Error updating record: " . $update->error;
    }
    $update->close();

    // Refresh the data to show updated values
    $stmt->execute();
    $benefit = $stmt->get_result()->fetch_assoc();
}

$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Benefits | HRIS</title>
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
    <h3>Edit Employee Benefits</h3>

    <?php if ($message): ?><div class="alert success"><?= $message ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert error"><?= $error ?></div><?php endif; ?>

    <form method="POST" class="form-container">
        <label>Health Plan:</label>
        <input type="text" name="health_plan" value="<?= htmlspecialchars($benefit['health_plan']) ?>" required>

        <label>Retirement Plan:</label>
        <input type="text" name="retirement_plan" value="<?= htmlspecialchars($benefit['retirement_plan']) ?>" required>

        <label>Insurance Type:</label>
        <input type="text" name="insurance_type" value="<?= htmlspecialchars($benefit['insurance_type']) ?>" required>

        <label>Dependents:</label>
        <input type="number" name="dependents" min="0" value="<?= htmlspecialchars($benefit['dependents']) ?>">

        <button type="submit" class="btn">Save Changes</button>
        <a href="benefits_list.php" class="btn-cancel">Cancel</a>
    </form>
</div>

<script src="assets/js/sidebar.js"></script>
</body>
</html>
