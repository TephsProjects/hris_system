<?php
session_start();
include('includes/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$employees = $conn->query("SELECT emp_id, CONCAT(first_name, ' ', last_name) AS full_name FROM employees ORDER BY first_name ASC");

$message = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emp_id = intval($_POST['emp_id']);
    $health_plan = $_POST['health_plan'] ?? '';
    $retirement_plan = $_POST['retirement_plan'] ?? '';
    $insurance_type = $_POST['insurance_type'] ?? '';
    $dependents = intval($_POST['dependents'] ?? 0);

    $stmt = $conn->prepare("INSERT INTO benefits (emp_id, health_plan, retirement_plan, insurance_type, dependents) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isssi", $emp_id, $health_plan, $retirement_plan, $insurance_type, $dependents);

    if ($stmt->execute()) {
        $message = "✅ Benefits successfully assigned.";
    } else {
        $error = "❌ Error: " . $stmt->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Benefits | HRIS</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <style>
        /* --- Enhanced Styling --- */
        .main-content {
            margin-left: 250px;
            padding: 40px;
            background-color: #f8f9fc;
            min-height: 100vh;
        }

        .main-content h3 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        .form-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            padding: 30px 40px;
            max-width: 600px;
            margin: 0 auto;
        }

        .form-container label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #444;
        }

        .form-container input[type="text"],
        .form-container input[type="number"],
        .form-container select {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
            transition: border 0.2s ease;
        }

        .form-container input:focus,
        .form-container select:focus {
            border-color: #007bff;
            outline: none;
        }

        .btn {
            display: inline-block;
            background: #007bff;
            color: #fff;
            font-weight: 600;
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .btn:hover {
            background: #0056b3;
        }

        .alert {
            margin-bottom: 20px;
            padding: 12px 18px;
            border-radius: 6px;
            font-weight: 500;
        }

        .alert.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        hr.sidebar-separator {
            border: none;
            border-top: 1px solid #ddd;
            margin: 12px 0;
        }

        .dropdown-content {
            display: none;
            list-style: none;
            padding-left: 15px;
        }

        .dropdown.active .dropdown-content {
            display: block;
        }
    </style>
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
                <li><a href="add_benefits.php" class="active">Add Benefits</a></li>
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

<!-- Main Content -->
<div class="main-content">
    <h3>Assign Employee Benefits</h3>

    <?php if ($message): ?><div class="alert success"><?= $message ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert error"><?= $error ?></div><?php endif; ?>

    <form method="POST" class="form-container">
        <label for="emp_id">Employee</label>
        <select name="emp_id" id="emp_id" required>
            <option value="">Select Employee</option>
            <?php while ($row = $employees->fetch_assoc()): ?>
                <option value="<?= $row['emp_id'] ?>"><?= htmlspecialchars($row['full_name']) ?></option>
            <?php endwhile; ?>
        </select>

        <label for="health_plan">Health Plan</label>
        <input type="text" name="health_plan" id="health_plan" placeholder="e.g., Standard HMO">

        <label for="retirement_plan">Retirement Plan</label>
        <input type="text" name="retirement_plan" id="retirement_plan" placeholder="e.g., 401(k)">

        <label for="insurance_type">Insurance Type</label>
        <input type="text" name="insurance_type" id="insurance_type" placeholder="e.g., Life, Accident, Health">

        <label for="dependents">Dependents</label>
        <input type="number" name="dependents" id="dependents" min="0" value="0">

        <button type="submit" class="btn">Assign Benefits</button>
    </form>
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
