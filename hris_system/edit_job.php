<?php
session_start();
include('includes/db.php');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$message = '';
$messageClass = '';

// Validate job_id
if (!isset($_GET['job_id']) || !is_numeric($_GET['job_id'])) {
    header("Location: job_list.php");
    exit();
}

$job_id = intval($_GET['job_id']);

// Fetch existing job details
$stmt = $conn->prepare("SELECT * FROM job_openings WHERE job_id = ?");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();
$job = $result->fetch_assoc();

if (!$job) {
    $message = "❌ Job not found.";
    $messageClass = "error";
}

// Update process
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $department = trim($_POST['department']);
    $description = trim($_POST['description']);
    $requirements = trim($_POST['requirements']);
    $status = $_POST['status'];

    if (empty($title) || empty($department)) {
        $message = "⚠️ Please fill in all required fields.";
        $messageClass = "error";
    } else {
        $update = $conn->prepare("UPDATE job_openings SET title=?, department=?, description=?, requirements=?, status=? WHERE job_id=?");
        $update->bind_param("sssssi", $title, $department, $description, $requirements, $status, $job_id);

        if ($update->execute()) {
            $message = "✅ Job details updated successfully!";
            $messageClass = "success";

            // Refresh data
            $stmt->execute();
            $result = $stmt->get_result();
            $job = $result->fetch_assoc();
        } else {
            $message = "❌ Error updating job: " . $conn->error;
            $messageClass = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Job Opening | HRIS</title>
<link rel="stylesheet" href="assets/css/style.css">
<style>
/* --- Page & Layout --- */
.main-content {
    padding: 40px 20px;
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
}

/* --- Form Card --- */
.job-card {
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    max-width: 600px;
    width: 100%;
}
.job-card h3 {
    text-align: center;
    color: #007bff;
    margin-bottom: 25px;
}

/* --- Messages --- */
.message {
    padding: 12px 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 600;
    text-align: center;
    animation: fadein 0.5s;
}
.message.success { background-color: #d4edda; color: #155724; }
.message.error { background-color: #f8d7da; color: #721c24; }
@keyframes fadein { from {opacity:0;} to {opacity:1;} }

/* --- Form Layout --- */
form {
    display: grid;
    grid-template-columns: 1fr;
    gap: 18px;
}

form label {
    font-weight: 600;
    color: #333;
}

form input, form textarea, form select {
    width: 100%;
    padding: 10px 12px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 14px;
    transition: 0.2s;
}

form input:focus, form select:focus, form textarea:focus {
    border-color: #007bff;
    outline: none;
}

textarea { resize: vertical; min-height: 80px; }

/* --- Buttons --- */
button {
    padding: 12px;
    border: none;
    border-radius: 8px;
    background: #007bff;
    color: #fff;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s;
}
button:hover { background: #0056b3; }

.back-btn {
    display: inline-block;
    margin-top: 10px;
    padding: 10px 16px;
    border-radius: 8px;
    background-color: #6c757d;
    color: #fff;
    text-decoration: none;
    font-size: 14px;
    transition: 0.2s;
}
.back-btn:hover { background-color: #5a6268; }

/* --- Responsive --- */
@media(max-width: 650px) {
    .main-content { padding: 20px 10px; }
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
            <!-- Performance Section -->
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
                <a href="#" class="dropdown-btn" onclick="toggleDropdown(event)" class="active">
                    Recruitment ▼
                </a>
                <ul class="dropdown-content">
                    <li><a href="add_job.php">Add Job Opening</a></li>
                    <li><a href="job_list.php" class="active">Job Openings</a></li>
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

<div class="main-content">
    <div class="job-card">
        <h3>Edit Job</h3>
        <?php if ($message): ?>
            <p class="message <?= $messageClass ?>"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <?php if ($job): ?>
        <form method="POST">
            <label>Job Title:</label>
            <input type="text" name="title" value="<?= htmlspecialchars($job['title']) ?>" required maxlength="100">

            <label>Department:</label>
            <input type="text" name="department" value="<?= htmlspecialchars($job['department']) ?>" required maxlength="100">

            <label>Description:</label>
            <textarea name="description"><?= htmlspecialchars($job['description']) ?></textarea>

            <label>Requirements:</label>
            <textarea name="requirements"><?= htmlspecialchars($job['requirements']) ?></textarea>

            <label>Status:</label>
            <select name="status">
                <option value="Open" <?= $job['status'] == 'Open' ? 'selected' : '' ?>>Open</option>
                <option value="Closed" <?= $job['status'] == 'Closed' ? 'selected' : '' ?>>Closed</option>
            </select>

            <button type="submit">Update Job</button>
        </form>
        <a href="job_list.php" class="back-btn">← Back to Job List</a>
        <?php endif; ?>
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
