<?php
session_start();
include('includes/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$message = '';
$messageClass = '';

// Fetch departments dynamically for dropdown
$departments = [];
$deptQuery = $conn->query("SELECT DISTINCT department FROM employees ORDER BY department ASC");
while ($row = $deptQuery->fetch_assoc()) {
    $departments[] = $row['department'];
}

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
        // Check for duplicate job title in same department
        $checkStmt = $conn->prepare("SELECT job_id FROM job_openings WHERE title=? AND department=?");
        $checkStmt->bind_param("ss", $title, $department);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        if ($checkResult->num_rows > 0) {
            $message = "⚠️ A job with this title already exists in this department.";
            $messageClass = "error";
        } else {
            $stmt = $conn->prepare("INSERT INTO job_openings (title, department, description, requirements, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $title, $department, $description, $requirements, $status);

            if ($stmt->execute()) {
                $message = "✅ Job opening added successfully!";
                $messageClass = "success";
                $_POST = []; // Clear form
            } else {
                $message = "❌ Database error: " . $conn->error;
                $messageClass = "error";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Job Opening | HRIS</title>
<link rel="stylesheet" href="assets/css/style.css">
<style>
/* Main Content Card */
.main-content {
    padding: 40px 20px;
    display: flex;
    justify-content: center;
}

.job-card {
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    max-width: 700px;
    width: 100%;
}

.job-card h3 {
    text-align: center;
    color: #007bff;
    margin-bottom: 25px;
}

/* Messages */
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

/* Form Layout */
form {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

form label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    color: #333;
}

form input[type="text"], form select, form textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 14px;
    transition: 0.2s;
}

form input[type="text"]:focus,
form select:focus,
form textarea:focus {
    border-color: #007bff;
    outline: none;
}

textarea {
    grid-column: 1 / 3;
    min-height: 100px;
    resize: vertical;
}

/* Button */
button {
    grid-column: 1 / 3;
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
button:hover {
    background: #0056b3;
}

/* Responsive */
@media(max-width: 600px) {
    form {
        grid-template-columns: 1fr;
    }
    textarea, button {
        grid-column: 1 / 2;
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
                    <li><a href="add_job.php" class="active">Add Job Opening</a></li>
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

<div class="main-content">
    <div class="job-card">
        <h3>Add Job Opening</h3>
        <?php if ($message): ?>
            <p class="message <?= $messageClass ?>"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <label>Job Title</label>
            <input type="text" name="title" value="<?= $_POST['title'] ?? '' ?>" maxlength="100" placeholder="e.g., Software Developer" required>

            <label>Department</label>
            <select name="department" required>
                <option value="">-- Select Department --</option>
                <?php foreach ($departments as $dept): 
                    $selected = (isset($_POST['department']) && $_POST['department']==$dept) ? 'selected' : '';
                ?>
                    <option value="<?= htmlspecialchars($dept) ?>" <?= $selected ?>><?= htmlspecialchars($dept) ?></option>
                <?php endforeach; ?>
            </select>

            <label>Status</label>
            <select name="status">
                <?php
                $statuses = ['Open', 'Closed'];
                foreach ($statuses as $s) {
                    $selected = (isset($_POST['status']) && $_POST['status'] == $s) ? 'selected' : '';
                    echo "<option value='$s' $selected>$s</option>";
                }
                ?>
            </select>

            <label>Description</label>
            <textarea name="description" placeholder="Brief job summary or responsibilities"><?= $_POST['description'] ?? '' ?></textarea>

            <label>Requirements</label>
            <textarea name="requirements" placeholder="List qualifications or skills required"><?= $_POST['requirements'] ?? '' ?></textarea>

            <button type="submit">Add Job</button>
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
<script>
setTimeout(() => {
    const msg = document.querySelector('.message');
    if(msg) msg.style.display = 'none';
}, 5000); // 5 seconds

function toggleDropdown(event) {
    event.preventDefault();
    const parent = event.target.closest('.dropdown');
    parent.classList.toggle('active');
}
</script>
</body>
</html>
