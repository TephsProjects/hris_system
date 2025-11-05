<?php
session_start();
include('includes/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Fetch employees for dropdown
$employees = $conn->query("SELECT emp_id, CONCAT(first_name, ' ', last_name) AS name FROM employees ORDER BY name ASC");

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_employees = isset($_POST['emp_ids']) ? $_POST['emp_ids'] : [];
    $title = trim($_POST['title']);
    $progress = isset($_POST['progress']) ? intval($_POST['progress']) : 0;
    $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;

    if (!empty($selected_employees) && !empty($title)) {
        $stmt = $conn->prepare("INSERT INTO trainings (emp_id, title, progress, due_date) VALUES (?, ?, ?, ?)");
        foreach ($selected_employees as $emp_id) {
            $emp_id = intval($emp_id);
            $stmt->bind_param("isis", $emp_id, $title, $progress, $due_date);
            $stmt->execute();
        }
        $message = "✅ Training assigned successfully to selected employees!";
    } else {
        $message = "⚠️ Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Training | HRIS</title>
<link rel="stylesheet" href="assets/css/style.css">
<style>
    .form-container {
        background: #fff;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        max-width: 700px;
        margin: 20px auto;
    }

    label {
        display: block;
        margin-top: 12px;
        font-weight: bold;
    }

    select, input[type="text"], input[type="date"], input[type="number"] {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border-radius: 6px;
        border: 1px solid #ccc;
    }

    select[multiple] {
        height: 150px;
    }

    button {
        margin-top: 20px;
        padding: 10px 18px;
        background: #007bff;
        border: none;
        color: #fff;
        border-radius: 6px;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    button:hover {
        background: #0056b3;
    }

    .message {
        margin: 10px 0;
        padding: 10px;
        border-radius: 6px;
        font-weight: bold;
    }

    .message.success {
        background: #d4edda;
        color: #155724;
    }

    .message.error {
        background: #f8d7da;
        color: #721c24;
    }

</style>
</head>
<body>
<div class="navbar">
    <h2>HR Information System</h2>
    <div class="user-info">
        <span>Welcome, <?php echo $_SESSION['full_name']; ?></span>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<!-- ✅ Your provided sidebar -->
<div class="sidebar">
    <ul>
        <li class="sidebar-section-title1">Tools</li>
        <li><a href="dashboard.php">Dashboard</a></li>

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

        <hr class="sidebar-separator">

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
                <li><a href="add_training.php" class="active">Add Training</a></li>
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

<!-- ✅ Main Content -->
<div class="main-content">
    <h3>Add Training</h3>
    <div class="form-container">
        <?php if ($message): ?>
            <div class="message <?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label>Select Employees:</label>
            <select name="emp_ids[]" multiple required>
                <?php while ($emp = $employees->fetch_assoc()): ?>
                    <option value="<?= $emp['emp_id'] ?>"><?= htmlspecialchars($emp['name']) ?></option>
                <?php endwhile; ?>
            </select>
            <small style="color:gray;">Hold CTRL (Windows) or CMD (Mac) to select multiple employees</small>

            <label>Training Title:</label>
            <input type="text" name="title" placeholder="e.g., Workplace Safety & Compliance" required>

            <label>Due Date:</label>
            <input type="date" name="due_date" required>

            <label>Initial Progress (%):</label>
            <input type="number" name="progress" min="0" max="100" value="0">

            <button type="submit">Add Training</button>
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
