<?php
session_start();
include('includes/db.php');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emp_id = $_POST['emp_id'];
    $evaluation_period = $_POST['evaluation_period'];
    $evaluator = $_POST['evaluator'];
    $rating = $_POST['rating'];
    $comments = $_POST['comments'];

    if (empty($emp_id) || empty($evaluation_period) || empty($evaluator) || empty($rating)) {
        $message = "❌ Please fill in all required fields.";
    } else {
        $stmt = $conn->prepare("INSERT INTO performance (emp_id, evaluation_period, evaluator, rating, comments) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issds", $emp_id, $evaluation_period, $evaluator, $rating, $comments);

        if ($stmt->execute()) {
            $message = "✅ Performance evaluation added successfully!";
        } else {
            $message = "❌ Error: " . $conn->error;
        }
    }
}

// Fetch employees for dropdown
$employees = $conn->query("SELECT emp_id, first_name, last_name FROM employees ORDER BY first_name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Performance Evaluation | HRIS</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .main-content {
    display: flex;
    justify-content: center;
    align-items: flex-start; /* top-aligned */
    padding: 40px 20px;
}

.performance-card {
    background: #fff;
    padding: 30px 40px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    max-width: 500px;
    width: 100%;
}

.performance-card h3 {
    text-align: center;
    color: #007bff;
    margin-bottom: 25px;
}

.performance-card form {
    display: flex;
    flex-direction: column;
}

.performance-card label {
    margin-top: 15px;
    font-weight: 600;
    color: #333;
}

.performance-card input,
.performance-card select,
.performance-card textarea {
    width: 100%;
    padding: 10px 12px;
    margin-top: 5px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 14px;
    transition: 0.2s;
}

.performance-card input:focus,
.performance-card select:focus,
.performance-card textarea:focus {
    border-color: #007bff;
    outline: none;
}

.performance-card button {
    margin-top: 20px;
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

.performance-card button:hover {
    background: #0056b3;
}

/* Message styling */
.message {
    padding: 12px 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 600;
    text-align: center;
}
.message.success { background-color: #d4edda; color: #155724; }
.message.error { background-color: #f8d7da; color: #721c24; }

/* Responsive */
@media(max-width: 600px) {
    .performance-card {
        padding: 20px;
    }
}

    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <h2>HR Information System</h2>
        <div class="user-info">
            <span>Welcome, <?php echo $_SESSION['full_name']; ?></span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <!-- Sidebar -->
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
                    <li><a href="add_performance.php" class="active">Add Evaluation</a></li>
                    <li><a href="performance_list.php">Evaluation List</a></li>
                </ul>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="performance-card">
            <h3>Add Performance Evaluation</h3>

            <?php if ($message): ?>
                <p class="message <?= strpos($message, '✅') === 0 ? 'success' : 'error'; ?>">
                    <?= $message; ?>
                </p>
            <?php endif; ?>

            <form method="POST">
                <label>Employee <span style="color:red">*</span></label>
                <select name="emp_id" required>
                    <option value="">Select Employee</option>
                    <?php while ($row = $employees->fetch_assoc()): ?>
                        <option value="<?= $row['emp_id']; ?>">
                            <?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label>Evaluation Period <span style="color:red">*</span></label>
                <input type="text" name="evaluation_period" placeholder="e.g., Q1 2025" required>

                <label>Evaluator <span style="color:red">*</span></label>
                <input type="text" name="evaluator" required>

                <label>Rating <span style="color:red">*</span></label>
                <input type="number" step="0.01" min="0" max="5" name="rating" placeholder="0.00 - 5.00" required>

                <label>Comments</label>
                <textarea name="comments" rows="4" placeholder="Optional comments..."></textarea>

                <button type="submit">Add Evaluation</button>
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
