<?php
session_start();
include('includes/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_no = $_POST['employee_no'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $address = $_POST['address'];
    $age = $_POST['age'];
    $civil_status = $_POST['civil_status'];
    $position = $_POST['position'];
    $date_hired = $_POST['date_hired'];
    $date_of_birth = $_POST['date_of_birth'];
    $department = $_POST['department'];
    $tax_status = $_POST['tax_status'];
    $tin_no = $_POST['tin_no'];
    $sss_no = $_POST['sss_no'];
    $philhealth_no = $_POST['philhealth_no'];
    $nationality = $_POST['nationality'];
    $gender = $_POST['gender'];
    $mobile_number = $_POST['mobile_number'];
    $email = $_POST['email'];
    $contact_person = $_POST['contact_person'];
    $emergency_contact = $_POST['emergency_contact'];
    $contract_end_date = $_POST['contract_end_date'];

    // Upload image
    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $file_name = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $file_name;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        $image = $file_name;
    }

    // Upload contract
    $contract_file = null;
    if (!empty($_FILES['contract_file']['name'])) {
        $contract_dir = "contracts/";
        if (!is_dir($contract_dir)) mkdir($contract_dir, 0777, true);
        $file_name = time() . "_" . basename($_FILES["contract_file"]["name"]);
        $target_file = $contract_dir . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (in_array($file_type, ['pdf', 'doc', 'docx'])) {
            move_uploaded_file($_FILES["contract_file"]["tmp_name"], $target_file);
            $contract_file = $file_name;
        } else {
            $message = "❌ Invalid file type. Only PDF or DOCX allowed.";
        }
    }

    $stmt = $conn->prepare("INSERT INTO employees 
        (employee_no, first_name, last_name, address, age, civil_status, date_of_birth, department, 
        tax_status, tin_no, sss_no, philhealth_no, nationality, gender, mobile_number, email, 
        contact_person, emergency_contact, image, contract_file, contract_end_date, position, date_hired)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "sssisssssssssssssssssss",
        $employee_no, $first_name, $last_name, $address, $age, $civil_status, $date_of_birth, $department,
        $tax_status, $tin_no, $sss_no, $philhealth_no, $nationality, $gender, $mobile_number, $email,
        $contact_person, $emergency_contact, $image, $contract_file, $contract_end_date, $position, $date_hired
    );

    if ($stmt->execute()) {
        $message = "✅ Employee added successfully with contract details!";
    } else {
        $message = "❌ Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Employee | HRIS</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/add_employee.css">
</head>
<style>
.two-column-form {
    display: grid;
    grid-template-columns: 1fr 1fr; /* Force exactly two columns */
    gap: 20px;
}

.two-column-form .form-group {
    display: flex;
    flex-direction: column;
}

/* Make the submit button span both columns */
.two-column-form button[type="submit"] {
    grid-column: 1 / span 2;
    padding: 10px 20px;
    margin-top: 10px;
}
</style>
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
                    <li><a href="add_employee.php" class="active">Add Employee</a></li>
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
                    <li><a href="add_performance.php">Add Evaluation</a></li>
                    <li><a href="performance_list.php">Evaluation List</a></li>
                </ul>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
<div class="main-content">
    <h3>Add New Employee</h3>

    <?php if ($message): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data" class="two-column-form">
        <div class="form-group">
            <label>Employee Number:</label>
            <input type="text" name="employee_no" required>
        </div>

        <div class="form-group">
            <label>First Name:</label>
            <input type="text" name="first_name" required>
        </div>

        <div class="form-group">
            <label>Last Name:</label>
            <input type="text" name="last_name" required>
        </div>

        <div class="form-group">
            <label>Date of Birth:</label>
            <input type="date" name="date_of_birth">
        </div>

        <div class="form-group">
            <label>Gender:</label>
            <select name="gender">
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>

        <div class="form-group">
            <label>Address:</label>
            <input type="text" name="address">
        </div>

        <div class="form-group">
            <label>Mobile Number:</label>
            <input type="text" name="mobile_number">
        </div>

        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email">
        </div>

        <div class="form-group">
            <label>Nationality:</label>
            <input type="text" name="nationality">
        </div>

        <div class="form-group">
            <label>Civil Status:</label>
            <select name="civil_status">
                <option value="Single">Single</option>
                <option value="Married">Married</option>
                <option value="Widowed">Widowed</option>
            </select>
        </div>

        <div class="form-group">
            <label>Department:</label>
            <input type="text" name="department">
        </div>

        <div class="form-group">
            <label>Position:</label>
            <input type="text" name="position">
        </div>

        <div class="form-group">
            <label>Tax Status:</label>
            <input type="text" name="tax_status">
        </div>

        <div class="form-group">
            <label>TIN Number:</label>
            <input type="text" name="tin_no">
        </div>

        <div class="form-group">
            <label>SSS Number:</label>
            <input type="text" name="sss_no">
        </div>

        <div class="form-group">
            <label>PhilHealth Number:</label>
            <input type="text" name="philhealth_no">
        </div>

        <div class="form-group">
            <label>Age:</label>
            <input type="number" name="age" min="18" max="99">
        </div>

        <div class="form-group">
            <label>Date Hired:</label>
            <input type="date" name="date_hired">
        </div>

        <!-- New Fields -->
        <div class="form-group">
            <label>Contact Person:</label>
            <input type="text" name="contact_person">
        </div>

        <div class="form-group">
            <label>Emergency Contact:</label>
            <input type="text" name="emergency_contact">
        </div>

        <div class="form-group">
            <label>Upload Image:</label>
            <input type="file" name="image" accept="image/*">
        </div>

        <div class="form-group">
            <label>Upload Contract File (PDF/DOCX):</label>
            <input type="file" name="contract_file" accept=".pdf,.doc,.docx">
        </div>

        <div class="form-group">
            <label>Contract End Date:</label>
            <input type="date" name="contract_end_date">
        </div>

        <button type="submit" class="btn">Add Employee</button>
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
