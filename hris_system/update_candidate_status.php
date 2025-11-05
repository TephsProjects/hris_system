<?php
session_start();
include('includes/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $candidate_id = intval($_POST['candidate_id']);
    $status = $_POST['status'];

    // Step 1: Update candidate status
    $stmt = $conn->prepare("UPDATE candidates SET status = ? WHERE candidate_id = ?");
    $stmt->bind_param("si", $status, $candidate_id);

    if ($stmt->execute()) {
        $response = "success";

        // Step 2: If candidate is hired, insert into employees
        if ($status === 'Hired') {
            // Fetch candidate info
            $getCandidate = $conn->prepare("
                SELECT full_name, email, phone, job_id 
                FROM candidates 
                WHERE candidate_id = ?
            ");
            $getCandidate->bind_param("i", $candidate_id);
            $getCandidate->execute();
            $candidate = $getCandidate->get_result()->fetch_assoc();
            $getCandidate->close();

            if ($candidate) {
                $full_name = trim($candidate['full_name']);
                $email = $candidate['email'];
                $mobile_number = $candidate['phone'];
                $job_id = $candidate['job_id'];

                // Split name into first and last
                $name_parts = explode(' ', $full_name);
                $first_name = $name_parts[0];
                $last_name = count($name_parts) > 1 ? end($name_parts) : '';

                // Get job info
                $getJob = $conn->prepare("SELECT title, department FROM job_openings WHERE job_id = ?");
                $getJob->bind_param("i", $job_id);
                $getJob->execute();
                $job = $getJob->get_result()->fetch_assoc();
                $getJob->close();

                $position = $job['title'] ?? '';
                $department = $job['department'] ?? '';

                // Check if already in employees
                $check = $conn->prepare("SELECT emp_id FROM employees WHERE email = ?");
                $check->bind_param("s", $email);
                $check->execute();
                $res = $check->get_result();

                if ($res->num_rows === 0) {
                    // Step 3: Insert into employees
                    $employee_no = 'EMP' . time();
                    $date_hired = date('Y-m-d');

                    $insert = $conn->prepare("
                        INSERT INTO employees (
                            employee_no, first_name, last_name, position, department, date_hired, email, mobile_number
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $insert->bind_param("ssssssss",
                        $employee_no,
                        $first_name,
                        $last_name,
                        $position,
                        $department,
                        $date_hired,
                        $email,
                        $mobile_number
                    );
                    $insert->execute();
                    $emp_id = $conn->insert_id;
                    $insert->close();

                    // ✅ Step 4: Automatically create default onboarding tasks
                    $tasks = [
                        ['Submit required documents', 3],
                        ['Attend company orientation', 7],
                        ['Workstation setup and IT account activation', 5]
                    ];

                    foreach ($tasks as $t) {
                        $taskName = $t[0];
                        $days = $t[1];
                        $dueDate = date('Y-m-d', strtotime("+$days days"));

                        $taskStmt = $conn->prepare("
                            INSERT INTO onboarding_tasks (emp_id, task_name, due_date)
                            VALUES (?, ?, ?)
                        ");
                        $taskStmt->bind_param("iss", $emp_id, $taskName, $dueDate);
                        $taskStmt->execute();
                        $taskStmt->close();
                    }

                    // ✅ Step 5: Automatically create default training assignments
                    $trainings = [
                        ['Workplace Safety Orientation', 0],
                        ['Data Privacy and Security Training', 0],
                        ['Company Culture and Values', 0]
                    ];

                    foreach ($trainings as $t) {
                        $title = $t[0];
                        $progress = $t[1];

                        $trainStmt = $conn->prepare("
                            INSERT INTO trainings (emp_id, title, progress)
                            VALUES (?, ?, ?)
                        ");
                        $trainStmt->bind_param("isi", $emp_id, $title, $progress);
                        $trainStmt->execute();
                        $trainStmt->close();
                    }

                    $response = "success|" . $emp_id;

                } else {
                    $existing = $res->fetch_assoc();
                    $response = "success|" . $existing['emp_id'];
                }

                $check->close();
            }
        }

        echo $response;
    } else {
        echo "error";
    }

    $stmt->close();
}
?>
