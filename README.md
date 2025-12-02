# hris_system

SQL

CREATE TABLE users (
  id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  full_name VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE employees (
  emp_id INT(11) AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  address VARCHAR(255),
  age INT(3),
  civil_status VARCHAR(50),
  position VARCHAR(100),
  date_hired DATE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE employees
ADD COLUMN date_of_birth DATE AFTER civil_status,
ADD COLUMN department VARCHAR(100) AFTER date_of_birth,
ADD COLUMN tax_status VARCHAR(100) AFTER department,
ADD COLUMN tin_no VARCHAR(20) AFTER tax_status,
ADD COLUMN sss_no VARCHAR(20) AFTER tin_no,
ADD COLUMN philhealth_no VARCHAR(20) AFTER sss_no,
ADD COLUMN nationality VARCHAR(50) AFTER philhealth_no,
ADD COLUMN gender VARCHAR(10) AFTER nationality,
ADD COLUMN mobile_number VARCHAR(20) AFTER gender,
ADD COLUMN email VARCHAR(100) AFTER mobile_number,
ADD COLUMN image VARCHAR(255) AFTER email;

ALTER TABLE employees
ADD COLUMN employee_no VARCHAR(20) NOT NULL UNIQUE AFTER emp_id;

ALTER TABLE employees
ADD COLUMN contact_person VARCHAR(255) AFTER email,
ADD COLUMN emergency_contact VARCHAR(50) AFTER contact_person;

ALTER TABLE employees
ADD COLUMN contract_file VARCHAR(255) AFTER image,
ADD COLUMN contract_end_date DATE AFTER contract_file;

ALTER TABLE employees
ADD COLUMN branch_id INT NULL AFTER department;

INSERT INTO users (username, password, full_name)
VALUES ('admin', '$2y$10$1/zeyFzWzmELhAea1GJikO1Mev4MJL2r/NbFb6cHNzPaLsGKziC96', 'Administrator');

CREATE TABLE branches (
    branch_id INT AUTO_INCREMENT PRIMARY KEY,
    branch_name VARCHAR(255) NOT NULL,
    address TEXT,
    contact_number VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE leaves (
    leave_id INT AUTO_INCREMENT PRIMARY KEY,
    emp_id INT NOT NULL,
    leave_type VARCHAR(50) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    reason TEXT,
    status ENUM('Pending', 'Approved', 'Denied') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (emp_id) REFERENCES employees(emp_id)
);

CREATE TABLE payroll (
    payroll_id INT AUTO_INCREMENT PRIMARY KEY,
    emp_id INT NOT NULL,
    payroll_month VARCHAR(20) NOT NULL,
    basic_salary DECIMAL(10,2) NOT NULL,
    allowances DECIMAL(10,2) DEFAULT 0,
    deductions DECIMAL(10,2) DEFAULT 0,
    net_salary DECIMAL(10,2) GENERATED ALWAYS AS (basic_salary + allowances - deductions) STORED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (emp_id) REFERENCES employees(emp_id)
);

ALTER TABLE payroll ADD COLUMN payment_type ENUM('monthly','biweekly') NOT NULL DEFAULT 'monthly';


CREATE TABLE performance (
    perf_id INT AUTO_INCREMENT PRIMARY KEY,
    emp_id INT NOT NULL,
    evaluation_period VARCHAR(50),
    evaluator VARCHAR(100),
    rating DECIMAL(3,2),
    comments TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (emp_id) REFERENCES employees(emp_id)
);


CREATE TABLE job_openings (
  job_id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(100),
  department VARCHAR(100),
  description TEXT,
  requirements TEXT,
  status ENUM('Open','Closed') DEFAULT 'Open',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE candidates (
  candidate_id INT AUTO_INCREMENT PRIMARY KEY,
  job_id INT,
  full_name VARCHAR(100),
  email VARCHAR(100),
  phone VARCHAR(20),
  resume VARCHAR(255),
  status ENUM('Applied','Screened','Interview','Hired','Rejected') DEFAULT 'Applied',
  applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (job_id) REFERENCES job_openings(job_id)
);

CREATE TABLE onboarding_tasks (
  task_id INT AUTO_INCREMENT PRIMARY KEY,
  emp_id INT,
  task_name VARCHAR(255),
  due_date DATE,
  status ENUM('Pending','Completed') DEFAULT 'Pending',
  FOREIGN KEY (emp_id) REFERENCES employees(emp_id)
);

INSERT INTO onboarding_tasks (emp_id, task_name, due_date, status)
VALUES
(EMP_ID, 'Submit Government IDs', DATE_ADD(CURDATE(), INTERVAL 3 DAY), 'Pending'),
(EMP_ID, 'Attend Company Orientation', DATE_ADD(CURDATE(), INTERVAL 5 DAY), 'Pending'),
(EMP_ID, 'Setup Company Email', DATE_ADD(CURDATE(), INTERVAL 2 DAY), 'Pending');

ALTER TABLE onboarding_tasks ADD COLUMN completed_at DATE NULL AFTER due_date;

CREATE TABLE trainings (
  training_id INT AUTO_INCREMENT PRIMARY KEY,
  emp_id INT,
  title VARCHAR(255),
  progress INT DEFAULT 0,
  completion_date DATE,
  FOREIGN KEY (emp_id) REFERENCES employees(emp_id)
);

CREATE TABLE benefits (
    benefit_id INT AUTO_INCREMENT PRIMARY KEY,
    emp_id INT NOT NULL,
    health_plan VARCHAR(255),
    retirement_plan VARCHAR(255),
    insurance_type VARCHAR(255),
    dependents INT DEFAULT 0,
    date_assigned DATE DEFAULT CURRENT_DATE,
    FOREIGN KEY (emp_id) REFERENCES employees(emp_id)
);

CREATE TABLE salary_history (
    history_id INT AUTO_INCREMENT PRIMARY KEY,
    emp_id INT NOT NULL,
    previous_salary DECIMAL(10,2),
    new_salary DECIMAL(10,2),
    change_reason VARCHAR(255),
    effective_date DATE DEFAULT CURRENT_DATE,
    FOREIGN KEY (emp_id) REFERENCES employees(emp_id)
);


INSERT INTO trainings (emp_id, title, progress)
VALUES
(EMP_ID, 'Workplace Safety Orientation', 0),
(EMP_ID, 'Data Privacy and Security Training', 0);

ALTER TABLE trainings 
ADD COLUMN due_date DATE NULL AFTER progress;
