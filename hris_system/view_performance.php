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

// Fetch performance evaluation with employee info
$stmt = $conn->prepare("
    SELECT p.*, e.first_name, e.last_name, e.employee_no
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Performance | HRIS</title>
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
            transition: 0.3s ease;
        }

        .eval-card h3 {
            margin-bottom: 25px;
            font-size: 1.8em;
            color: #333;
            border-bottom: 2px solid #17a2b8;
            padding-bottom: 10px;
        }

        .eval-card p {
            margin: 12px 0;
            font-size: 1.05em;
            color: #555;
        }

        .eval-card strong {
            color: #333;
        }

        .rating-bar {
            height: 15px;
            width: 100%;
            background: #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 5px;
        }

        .rating-fill {
            height: 100%;
            background: #28a745;
            width: 0%;
            text-align: right;
            padding-right: 5px;
            color: #fff;
            font-size: 0.8em;
            line-height: 15px;
        }

        .buttons {
            margin-top: 25px;
        }

        .buttons a {
            display: inline-block;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 6px;
            color: #fff;
            margin-right: 10px;
            font-weight: 500;
            transition: 0.2s ease;
        }

        .back-btn { background: #6c757d; }
        .back-btn:hover { background: #5a6268; }

        .edit-btn { background: #28a745; }
        .edit-btn:hover { background: #218838; }

        .delete-btn { background: #dc3545; }
        .delete-btn:hover { background: #c82333; }

        @media (max-width: 768px) {
            .eval-card {
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
                    <li><a href="training_list.php" class="active">Trainings</a></li>
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
            <h3>Performance Evaluation Details</h3>
            <p><strong>Employee No:</strong> <?php echo htmlspecialchars($evaluation['employee_no']); ?></p>
            <p><strong>Employee Name:</strong> <?php echo htmlspecialchars($evaluation['first_name'] . ' ' . $evaluation['last_name']); ?></p>
            <p><strong>Evaluation Period:</strong> <?php echo htmlspecialchars($evaluation['evaluation_period']); ?></p>
            <p><strong>Evaluator:</strong> <?php echo htmlspecialchars($evaluation['evaluator']); ?></p>
            <p><strong>Rating:</strong>
                <div class="rating-bar">
                    <div class="rating-fill" style="width: <?php echo htmlspecialchars($evaluation['rating']*20); ?>%;">
                        <?php echo htmlspecialchars($evaluation['rating']); ?>
                    </div>
                </div>
            </p>
            <p><strong>Comments:</strong><br><?php echo nl2br(htmlspecialchars($evaluation['comments'])); ?></p>
            <p><strong>Created At:</strong> <?php echo htmlspecialchars($evaluation['created_at']); ?></p>

            <div class="buttons">
                <a href="performance_list.php" class="back-btn">← Back</a>
                <a href="edit_performance.php?id=<?php echo $evaluation['perf_id']; ?>" class="edit-btn">✏️ Edit</a>
                <a href="delete_performance.php?id=<?php echo $evaluation['perf_id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this evaluation?');">🗑️ Delete</a>
            </div>
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
