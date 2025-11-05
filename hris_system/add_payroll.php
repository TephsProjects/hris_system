<?php
session_start();
include('includes/db.php');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Fetch employees for dropdown
$empResult = $conn->query("SELECT emp_id, CONCAT(first_name, ' ', last_name) AS full_name FROM employees ORDER BY first_name ASC");

// Initialize form values
$emp_id = '';
$payment_type = '';
$basic_salary = '';
$allowances = '';
$deductions = '';
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emp_id = intval($_POST['emp_id']);
    $payment_type = $_POST['payment_type'] ?? '';
    $basic_salary = floatval($_POST['basic_salary']);
    $allowances = floatval($_POST['allowances']);
    $deductions = floatval($_POST['deductions']);

    // Determine pay period
    $current_month = date('Y-m');
    if ($payment_type === 'monthly') {
        $pay_period = $current_month;
    } else {
        $day = date('d');
        $cutoff = ($day <= 15) ? 'First Cut-Off' : 'Second Cut-Off';
        $pay_period = $current_month . '-' . $cutoff;
    }

    // Check for duplicate payroll
    $checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM payroll WHERE emp_id = ? AND payroll_month = ? AND payment_type = ?");
    $checkStmt->bind_param("iss", $emp_id, $pay_period, $payment_type);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result()->fetch_assoc();
    $checkStmt->close();

    if ($checkResult['count'] > 0) {
        $error = "Payroll for this employee and period already exists!";
    } else {
        // Insert payroll
        $stmt = $conn->prepare("INSERT INTO payroll (emp_id, payroll_month, payment_type, basic_salary, allowances, deductions) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issddd", $emp_id, $pay_period, $payment_type, $basic_salary, $allowances, $deductions);

        if ($stmt->execute()) {
            $message = "Payroll added successfully for period: $pay_period.";

            // Record salary history
            $prev_salary_query = $conn->prepare("SELECT basic_salary FROM payroll WHERE emp_id = ? ORDER BY payroll_id DESC LIMIT 1");
            $prev_salary_query->bind_param("i", $emp_id);
            $prev_salary_query->execute();
            $prev_salary_result = $prev_salary_query->get_result()->fetch_assoc();
            $previous_salary = $prev_salary_result['basic_salary'] ?? 0;
            $prev_salary_query->close();

            $reason = "New payroll entry ($payment_type)";
            $history_stmt = $conn->prepare("INSERT INTO salary_history (emp_id, previous_salary, new_salary, change_reason) VALUES (?, ?, ?, ?)");
            $history_stmt->bind_param("idds", $emp_id, $previous_salary, $basic_salary, $reason);
            $history_stmt->execute();
            $history_stmt->close();

            // Reset form
            $emp_id = $payment_type = $basic_salary = $allowances = $deductions = '';
        } else {
            $error = "Error: " . $stmt->error;
        }

        $stmt->close(); // close only once
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Payroll | HRIS</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Main content centering */
        .main-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        /* Payroll form container */
        .form-container {
            max-width: 500px;
            width: 100%;
            background: #fff;
            padding: 30px 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            margin-top: 20px;
        }

        .form-container h3 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.6rem;
            color: #333;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: 500;
            color: #555;
        }

        input, select {
            width: 100%;
            padding: 10px 12px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
            transition: border 0.3s;
        }

        input {
            width: 95%;
            padding: 10px 12px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
            transition: border 0.3s;
        }

        input:focus, select:focus {
            border-color: #007bff;
            outline: none;
        }

        /* Button */
        .btn {
            margin-top: 20px;
            padding: 12px 20px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: background 0.3s, transform 0.2s;
            width: 100%;
        }

        .btn:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }

        /* Alerts */
        .alert {
            padding: 12px 15px;
            margin: 15px 0;
            border-radius: 8px;
            text-align: center;
            width: 100%;
            max-width: 500px;
            font-weight: 500;
        }

        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }

        /* Net Salary Display */
        .net-salary {
            margin-top: 15px;
            font-weight: bold;
            font-size: 1.1rem;
            color: #007bff;
            text-align: center;
        }

        /* Responsive */
        @media screen and (max-width: 600px) {
            .form-container {
                padding: 20px 15px;
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
                <li><a href="add_payroll.php" class="active">Add Payroll</a></li>
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

<div class="main-content">
    <h3>Add Payroll</h3>

    <?php if ($message): ?>
        <div class="alert success"><?= $message ?></div>
    <?php elseif ($error): ?>
        <div class="alert error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="add_payroll.php" class="form-container" id="payrollForm">
        <label>Employee:</label>
        <select name="emp_id" required>
            <option value="">Select Employee</option>
            <?php foreach ($empResult as $row): ?>
                <option value="<?= $row['emp_id'] ?>" <?= $row['emp_id'] == $emp_id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($row['full_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Payment Type:</label>
        <select name="payment_type" id="paymentType" required>
            <option value="">Select Payment Type</option>
            <option value="monthly" <?= $payment_type === 'monthly' ? 'selected' : '' ?>>Monthly</option>
            <option value="biweekly" <?= $payment_type === 'biweekly' ? 'selected' : '' ?>>Bi-Weekly (Per Cutoff)</option>
        </select>

        <label>Basic Salary:</label>
        <input type="number" step="0.01" min="0" name="basic_salary" value="<?= htmlspecialchars($basic_salary) ?>" required>

        <label>Allowances:</label>
        <input type="number" step="0.01" min="0" name="allowances" value="<?= htmlspecialchars($allowances ?: 0) ?>">

        <label>Deductions:</label>
        <input type="number" step="0.01" min="0" name="deductions" value="<?= htmlspecialchars($deductions ?: 0) ?>">

        <div class="net-salary" id="netSalary">Net Salary: 0.00</div>

        <button type="submit" class="btn">Add Payroll</button>
    </form>
</div>


<script>
function toggleDropdown(event) {
    event.preventDefault();
    const parent = event.target.closest('.dropdown');
    parent.classList.toggle('active');
}

// Live net salary calculation
const basicInput = document.querySelector('[name="basic_salary"]');
const allowanceInput = document.querySelector('[name="allowances"]');
const deductionInput = document.querySelector('[name="deductions"]');
const netSalaryDiv = document.getElementById('netSalary');

function calculateNet() {
    const basic = parseFloat(basicInput.value) || 0;
    const allowance = parseFloat(allowanceInput.value) || 0;
    const deduction = parseFloat(deductionInput.value) || 0;
    const net = basic + allowance - deduction;
    netSalaryDiv.innerText = "Net Salary: " + net.toFixed(2);
}

[basicInput, allowanceInput, deductionInput].forEach(input => input.addEventListener('input', calculateNet));
calculateNet(); // Initial calculation
</script>
</body>
</html>
