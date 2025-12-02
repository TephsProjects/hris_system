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

// Fetch job openings
$jobs = $conn->query("SELECT job_id, title FROM job_openings WHERE status='Open' ORDER BY title ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $job_id = isset($_POST['job_id']) ? intval($_POST['job_id']) : 0;
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $status = $_POST['status'];

    // Basic validation
    if (empty($job_id) || empty($full_name) || empty($email)) {
        $message = "⚠️ Please fill in all required fields.";
        $messageClass = "error";
    } else {
        // Handle resume upload
        $resume = null;
        if (!empty($_FILES['resume']['name'])) {
            $target_dir = "uploads/resumes/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

            $file_name = time() . "_" . basename($_FILES["resume"]["name"]);
            $target_file = $target_dir . $file_name;
            $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Allow only specific file types
            $allowed_types = ['pdf', 'doc', 'docx'];
            if (in_array($file_type, $allowed_types) && $_FILES["resume"]["size"] <= 5 * 1024 * 1024) {
                if (move_uploaded_file($_FILES["resume"]["tmp_name"], $target_file)) {
                    $resume = $file_name;
                } else {
                    $message = "❌ Error uploading resume file.";
                    $messageClass = "error";
                }
            } else {
                $message = "⚠️ Invalid file type or size (max 5MB). Allowed: PDF, DOC, DOCX.";
                $messageClass = "error";
            }
        }

        // Insert candidate data
        if (empty($message)) {
            $stmt = $conn->prepare("INSERT INTO candidates (job_id, full_name, email, phone, resume, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssss", $job_id, $full_name, $email, $phone, $resume, $status);

            if ($stmt->execute()) {
                $message = "✅ Candidate added successfully!";
                $messageClass = "success";
                // Optionally clear form
                $_POST = [];
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
<title>Add Candidate | HRIS</title>
<link rel="stylesheet" href="assets/css/style.css">
<style>
.main-content {
    padding: 40px 20px;
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 30px;
}
/* Form Card */
.candidate-card {
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    max-width: 600px;
    width: 100%;
}

.candidate-card h3 {
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

form input, form select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 14px;
    transition: 0.2s;
}

form input:focus, form select:focus {
    border-color: #007bff;
    outline: none;
}

form input[type="file"] {
    grid-column: 1 / 3;
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
button:hover { background: #0056b3; }

/* Recent Jobs Card */
.recent-jobs {
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    max-width: 300px;
    width: 100%;
    height: fit-content;
}
.recent-jobs h4 {
    text-align: center;
    margin-bottom: 15px;
    color: #28a745;
}
.recent-jobs ul {
    list-style: none;
    padding: 0;
    margin: 0;
}
.recent-jobs li {
    padding: 8px 10px;
    border-bottom: 1px solid #f0f0f0;
    font-weight: 500;
    color: #333;
}
.recent-jobs li:last-child { border-bottom: none; }

/* Responsive */
@media(max-width: 900px) {
    .main-content {
        flex-direction: column;
        align-items: center;
    }
}
</style>
</head>
<body>
<div class="navbar">
    <h2>HR Information System</h2>
    <div class="user-info">
        <span>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
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
                    <li><a href="job_list.php">Job Openings</a></li>
                    <li><a href="add_candidate.php" class="active">Add Candidate</a></li>
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
    <!-- Candidate Form -->
    <div class="candidate-card">
        <h3>Add Candidate</h3>
        <?php if ($message): ?>
            <p class="message <?= $messageClass ?>"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <label>Job Opening:</label>
            <select name="job_id" required>
                <option value="">Select Job</option>
                <?php while ($job = $jobs->fetch_assoc()): ?>
                    <option value="<?= $job['job_id'] ?>" <?= (isset($_POST['job_id']) && $_POST['job_id'] == $job['job_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($job['title']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Full Name:</label>
            <input type="text" name="full_name" value="<?= $_POST['full_name'] ?? '' ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?= $_POST['email'] ?? '' ?>" required>

            <label>Phone:</label>
            <input type="text" name="phone" value="<?= $_POST['phone'] ?? '' ?>">

            <label>Resume (PDF/DOC/DOCX):</label>
            <input type="file" name="resume" accept=".pdf,.doc,.docx">

            <label>Status:</label>
            <select name="status">
                <?php
                $statuses = ['Applied', 'Screened', 'Interview', 'Hired', 'Rejected'];
                foreach ($statuses as $s) {
                    $selected = (isset($_POST['status']) && $_POST['status'] == $s) ? 'selected' : '';
                    echo "<option value='$s' $selected>$s</option>";
                }
                ?>
            </select>

            <button type="submit">Add Candidate</button>
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
