<?php
session_start();
include('includes/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid Branch ID.");
}

$id = $_GET['id'];
$message = "";

// Fetch branch
$stmt = $conn->prepare("SELECT * FROM branches WHERE branch_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$branch = $stmt->get_result()->fetch_assoc();

if (!$branch) {
    die("Branch not found.");
}

// Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['branch_name']);
    $address = trim($_POST['address']);

    if ($name == "") {
        $message = "Branch name is required!";
    } else {
        $update = $conn->prepare("UPDATE branches SET branch_name = ?, address = ? WHERE branch_id = ?");
        $update->bind_param("ssi", $name, $address, $id);

        if ($update->execute()) {
            $message = "Branch updated successfully!";
        } else {
            $message = "Error updating branch.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Branch</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
    <h2>HR Information System</h2>
    <div class="user-info">
        <span>Welcome, <?= htmlspecialchars($_SESSION['full_name']); ?></span>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<!-- SIDEBAR (FULL COPY) -->
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
            <a href="#" class="dropdown-btn" onclick="toggleDropdown(event)">Reports & Accounts ▼</a>
            <ul class="dropdown-content">
                <li><a href="reports.php">Reports</a></li>
                <li><a href="accounts.php">Accounts</a></li>
                <li><a href="add_employee.php">Add Employee</a></li>
            </ul>
        </li>

        <hr class="sidebar-separator">

        <li class="sidebar-section-title">Recruitments and Hiring</li>

        <li class="dropdown">
            <a href="#" class="dropdown-btn" onclick="toggleDropdown(event)">Recruitment ▼</a>
            <ul class="dropdown-content">
                <li><a href="add_job.php">Add Job Opening</a></li>
                <li><a href="job_list.php">Job Openings</a></li>
                <li><a href="add_candidate.php">Add Candidate</a></li>
                <li><a href="candidate_list.php">Candidates</a></li>
            </ul>
        </li>

        <hr class="sidebar-separator">

        <li class="sidebar-section-title">Utilities</li>

        <li class="dropdown active">
            <a href="#" class="dropdown-btn" onclick="toggleDropdown(event)">Branch / Location ▼</a>
            <ul class="dropdown-content" style="display:block;">
                <li><a href="add_branch.php">Add Branch / Location</a></li>
                <li><a href="branch_list.php">Branch / Location List</a></li>
            </ul>
        </li>

        <hr class="sidebar-separator">

        <li class="sidebar-section-title">Onboarding & Training</li>

        <li class="dropdown">
            <a href="#" class="dropdown-btn" onclick="toggleDropdown(event)">Onboarding & Training ▼</a>
            <ul class="dropdown-content">
                <li><a href="onboarding_list.php">Onboarding Tasks</a></li>
                <li><a href="add_onboarding.php">Add Onboarding Task</a></li>
                <li><a href="training_list.php">Trainings</a></li>
                <li><a href="add_training.php">Add Training</a></li>
            </ul>
        </li>

        <hr class="sidebar-separator">

        <li class="sidebar-section-title">Performance Management</li>

        <li class="dropdown">
            <a href="#" class="dropdown-btn" onclick="toggleDropdown(event)">Evaluation ▼</a>
            <ul class="dropdown-content">
                <li><a href="add_performance.php">Add Evaluation</a></li>
                <li><a href="performance_list.php">Evaluation List</a></li>
            </ul>
        </li>
    </ul>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">
    <h2>Edit Branch / Location</h2>

    <?php if ($message): ?>
        <p style="color:green;"><?= htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Branch Name</label>
        <input type="text" name="branch_name" value="<?= htmlspecialchars($branch['branch_name']); ?>" required>

        <label>Address</label>
        <textarea name="address"><?= htmlspecialchars($branch['address']); ?></textarea>

        <button type="submit" class="btn">Update Branch</button>
    </form>

</div>

<script>
function toggleDropdown(event) {
    event.preventDefault();
    const parent = event.target.closest('.dropdown');
    parent.classList.toggle('active');
    const content = parent.querySelector('.dropdown-content');
    if (content) {
        content.style.display = content.style.display === 'block' ? 'none' : 'block';
    }
}
</script>

</body>
</html>
