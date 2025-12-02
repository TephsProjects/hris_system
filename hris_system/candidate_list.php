<?php
session_start();
include('includes/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Optional filter by job
$where = "";
if (isset($_GET['job_id']) && is_numeric($_GET['job_id'])) {
    $job_id = $_GET['job_id'];
    $where = "WHERE c.job_id = $job_id";
}

$sql = "SELECT c.*, j.title AS job_title 
        FROM candidates c 
        LEFT JOIN job_openings j ON c.job_id = j.job_id
        $where
        ORDER BY c.applied_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Candidates | HRIS</title>
<link rel="stylesheet" href="assets/css/style.css">
<style>
    tr:nth-child(even) {
        background-color: #f8f9fa;
    }

    tr:hover {
        background-color: #e9f5ff;
    }

    /* Default look */
    .status-select {
        padding: 5px 8px;
        border-radius: 6px;
        border: 1px solid #ccc;
        background: #fff;
        font-weight: bold;
    }

    /* Status color coding */
    .status-applied {
        background-color: #f0f0f0;
        color: #555;
    }

    .status-screened {
        background-color: #d1ecf1;
        color: #0c5460;
    }

    .status-interview {
        background-color: #fff3cd;
        color: #856404;
    }

    .status-hired {
        background-color: #d4edda;
        color: #155724;
    }

    .status-rejected {
        background-color: #f8d7da;
        color: #721c24;
    }

    .status-select:focus {
        outline: none;
        border-color: #007bff;
    }

    /* Message banner */
    #statusMsg {
        margin: 10px 0;
        padding: 10px;
        font-weight: bold;
        display: none;
        border-radius: 5px;
    }

    #statusMsg.success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    #statusMsg.error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
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
                    <li><a href="add_candidate.php">Add Candidate</a></li>
                    <li><a href="candidate_list.php" class="active">Candidates</a></li>
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
    <h3>Candidates List</h3>
    <div id="statusMsg"></div>

    <table>
        <tr>
            <th>Full Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Job Title</th>
            <th>Status</th>
            <th>Resume</th>
            <th>Applied At</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['full_name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?= htmlspecialchars($row['job_title']) ?></td>
            <td>
                <select class="status-select" 
                        onchange="updateStatus(<?= $row['candidate_id'] ?>, this.value); setStatusColor(this);" 
                        id="status-<?= $row['candidate_id'] ?>">
                    <option value="Applied"   <?= $row['status'] == 'Applied' ? 'selected' : '' ?>>Applied</option>
                    <option value="Screened"  <?= $row['status'] == 'Screened' ? 'selected' : '' ?>>Screened</option>
                    <option value="Interview" <?= $row['status'] == 'Interview' ? 'selected' : '' ?>>Interview</option>
                    <option value="Hired"     <?= $row['status'] == 'Hired' ? 'selected' : '' ?>>Hired</option>
                    <option value="Rejected"  <?= $row['status'] == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
            </td>
            <td>
                <?php if ($row['resume']): ?>
                    <a href="uploads/resumes/<?= htmlspecialchars($row['resume']) ?>" target="_blank">View</a>
                <?php else: ?>
                    —
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($row['applied_at']) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<script>
// Sidebar toggle
function toggleDropdown(event) {
    event.preventDefault();
    const parent = event.target.closest('.dropdown');
    parent.classList.toggle('active');
}

// Apply status color to the dropdown
function setStatusColor(select) {
    select.classList.remove(
        'status-applied',
        'status-screened',
        'status-interview',
        'status-hired',
        'status-rejected'
    );

    const value = select.value.toLowerCase();
    select.classList.add('status-' + value);
}

// Update candidate status via AJAX
function updateStatus(candidateId, newStatus) {
    fetch('update_candidate_status.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'candidate_id=' + candidateId + '&status=' + encodeURIComponent(newStatus)
    })
    .then(response => response.text())
    .then(data => {
        const msg = document.getElementById('statusMsg');
        msg.style.display = 'block';
        if (data.startsWith('success')) {
            msg.className = 'success';
            msg.textContent = 'Candidate status updated successfully.';

            // If the candidate was hired, redirect to edit page
            if (newStatus === 'Hired' && data.includes('|')) {
                const empId = data.split('|')[1];
                setTimeout(() => {
                    window.location.href = 'edit_employee.php?emp_id=' + empId;
                }, 1000);
            }
        } else {
            msg.className = 'error';
            msg.textContent = 'Error updating candidate status.';
        }
        setTimeout(() => msg.style.display = 'none', 2500);
    })
    .catch(err => {
        console.error(err);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.status-select').forEach(setStatusColor);
});

</script>
</body>
</html>
