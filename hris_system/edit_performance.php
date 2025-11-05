<?php
session_start();
include('includes/db.php');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Check if evaluation ID is provided
if (!isset($_GET['id'])) {
    header("Location: performance_list.php");
    exit();
}

$perf_id = $_GET['id'];
$message = '';

// Fetch performance evaluation with employee info
$stmt = $conn->prepare("
    SELECT p.*, e.first_name, e.last_name, e.emp_id, e.employee_no
    FROM performance p
    JOIN employees e ON p.emp_id = e.emp_id
    WHERE p.perf_id = ?
");
$stmt->bind_param("i", $perf_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Evaluation not found.";
    exit();
}

$evaluation = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emp_id = $_POST['emp_id'];
    $evaluation_period = $_POST['evaluation_period'];
    $evaluator = $_POST['evaluator'];
    $rating = $_POST['rating'];
    $comments = $_POST['comments'];

    $update = $conn->prepare("
        UPDATE performance
        SET emp_id=?, evaluation_period=?, evaluator=?, rating=?, comments=?
        WHERE perf_id=?
    ");
    $update->bind_param(
        "issdsi",
        $emp_id, $evaluation_period, $evaluator, $rating, $comments, $perf_id
    );

    if ($update->execute()) {
        $message = "✅ Performance evaluation updated successfully!";
        // Refresh data
        $stmt->execute();
        $result = $stmt->get_result();
        $evaluation = $result->fetch_assoc();
    } else {
        $message = "❌ Error updating evaluation: " . $conn->error;
    }
}

// Fetch all employees for dropdown
$emp_result = $conn->query("SELECT emp_id, employee_no, first_name, last_name FROM employees ORDER BY first_name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Performance | HRIS</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .main-content {
            display: flex;
            justify-content: center;
            padding: 40px 20px;
        }

        .eval-card {
            max-width: 700px;
            width: 100%;
            background: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .eval-card h3 {
            margin-bottom: 25px;
            font-size: 1.8em;
            color: #333;
            border-bottom: 2px solid #17a2b8;
            padding-bottom: 10px;
        }

        .eval-card .form-group {
            margin-bottom: 15px;
        }

        .eval-card label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .eval-card input, .eval-card select, .eval-card textarea {
            width: 100%;
            padding: 8px 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1em;
        }

        .eval-card textarea {
            resize: vertical;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            color: #fff;
            font-weight: 500;
            cursor: pointer;
            background: #28a745;
            transition: 0.2s ease;
        }

        .btn:hover {
            background: #218838;
        }

        .message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 6px;
        }

        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
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
        <div class="eval-card">
            <h3>Edit Performance Evaluation</h3>

            <?php if ($message): ?>
                <div class="message <?php echo strpos($message, '✅') === 0 ? 'success' : 'error'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Employee:</label>
                    <select name="emp_id" required>
                        <?php while($emp = $emp_result->fetch_assoc()): ?>
                            <option value="<?php echo $emp['emp_id']; ?>" <?php if($emp['emp_id'] == $evaluation['emp_id']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($emp['employee_no'] . ' - ' . $emp['first_name'] . ' ' . $emp['last_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Evaluation Period:</label>
                    <input type="text" name="evaluation_period" value="<?php echo htmlspecialchars($evaluation['evaluation_period']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Evaluator:</label>
                    <input type="text" name="evaluator" value="<?php echo htmlspecialchars($evaluation['evaluator']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Rating (0.00 - 5.00):</label>
                    <input type="number" name="rating" step="0.01" min="0" max="5" value="<?php echo htmlspecialchars($evaluation['rating']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Comments:</label>
                    <textarea name="comments" rows="5"><?php echo htmlspecialchars($evaluation['comments']); ?></textarea>
                </div>

                <button type="submit" class="btn">💾 Save Changes</button>
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
