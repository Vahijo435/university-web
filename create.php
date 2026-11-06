<?php
$pdo = new PDO(
    'mysql:host=localhost;port=3307;charset=utf8mb4',
    'root',
    '',
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
);

$sql = "CREATE DATABASE IF NOT EXISTS university_db";
$pdo->exec($sql);

$pdo->exec("USE university_db");

$sql = "CREATE TABLE IF NOT EXISTS user_roles (
    role_id INT NOT NULL,
    role_name VARCHAR(30) NOT NULL,
    PRIMARY KEY (role_id),
    UNIQUE (role_name)
)";
$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS Users (
    User_ID INT NOT NULL,
    Username VARCHAR(30) NOT NULL,
    Password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(30) NOT NULL,
    role_id INT NOT NULL,
    PRIMARY KEY (User_ID),
    UNIQUE (Username),
    UNIQUE (email),
    FOREIGN KEY (role_id) REFERENCES user_roles(role_id)
)";
$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS academic_titles (
    academic_title_id INT NOT NULL,
    academic_title_name VARCHAR(30) NOT NULL,
    PRIMARY KEY (academic_title_id),
    UNIQUE (academic_title_name)
)";
$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS departments (
    department_ID INT NOT NULL,
    department_name VARCHAR(30) NOT NULL,
    PRIMARY KEY (department_ID),
    UNIQUE (department_name)
)";
$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS specialties (
    specialty_ID INT NOT NULL,
    department_id INT NOT NULL,
    specialty_name VARCHAR(30) NOT NULL,
    PRIMARY KEY (specialty_ID),
    FOREIGN KEY (department_id) REFERENCES departments(department_ID)
)";
$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS semesters (
    semester_id INT NOT NULL,
    semester_name VARCHAR(30) NOT NULL,
    PRIMARY KEY (semester_id),
    UNIQUE (semester_name)
)";
$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS subjects (
    subject_id INT NOT NULL,
    department_id INT NOT NULL,
    subject_name VARCHAR(30) NOT NULL,
    credits INT NOT NULL CHECK (credits > 0),
    PRIMARY KEY (subject_id),
    FOREIGN KEY (department_id) REFERENCES departments(department_ID)
)";
$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS enrollment_status (
    enrollment_status_id INT NOT NULL,
    enrollment_status_name VARCHAR(30) NOT NULL,
    PRIMARY KEY (enrollment_status_id),
    UNIQUE (enrollment_status_name)
)";
$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS action_grade_history (
    action_id INT NOT NULL,
    action_name VARCHAR(50) NOT NULL,
    PRIMARY KEY (action_id),
    UNIQUE (action_name)
)";
$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS scholarship_application_status (
    application_status_id INT NOT NULL,
    status_name VARCHAR(50) NOT NULL,
    PRIMARY KEY (application_status_id),
    UNIQUE (status_name)
)";
$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS teachers (
    teacher_id INT NOT NULL,
    user_id INT NOT NULL,
    department_id INT NOT NULL,
    teacher_name VARCHAR(30) NOT NULL,
    phone_number VARCHAR(30),
    hire_date DATE,
    academic_title_id INT NOT NULL,
    UCN VARCHAR(30),
    PRIMARY KEY (teacher_id),
    UNIQUE (user_id),
    UNIQUE (UCN),
    FOREIGN KEY (user_id) REFERENCES Users(User_ID),
    FOREIGN KEY (department_id) REFERENCES departments(department_ID),
    FOREIGN KEY (academic_title_id) REFERENCES academic_titles(academic_title_id)
)";
$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS Students (
    Student_ID INT NOT NULL,
    User_id INT NOT NULL,
    Specialty_ID INT NOT NULL,
    faculty_number VARCHAR(30) NOT NULL,
    student_name VARCHAR(30) NOT NULL,
    phone_number VARCHAR(30),
    UCN VARCHAR(30),
    PRIMARY KEY (Student_ID),
    UNIQUE (User_id),
    UNIQUE (faculty_number),
    UNIQUE (UCN),
    FOREIGN KEY (User_id) REFERENCES Users(User_ID),
    FOREIGN KEY (Specialty_ID) REFERENCES specialties(specialty_ID)
)";
$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS open_courses (
    open_course_id INT NOT NULL,
    subject_id INT NOT NULL,
    semester_id INT NOT NULL,
    PRIMARY KEY (open_course_id),
    UNIQUE (subject_id, semester_id),
    FOREIGN KEY (subject_id) REFERENCES subjects(subject_id),
    FOREIGN KEY (semester_id) REFERENCES semesters(semester_id)
)";
$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS teacher_assignments (
    teacher_assignment_id INT NOT NULL,
    open_course_id INT NOT NULL,
    teacher_id INT NOT NULL,
    PRIMARY KEY (teacher_assignment_id),
    UNIQUE (open_course_id, teacher_id),
    FOREIGN KEY (open_course_id) REFERENCES open_courses(open_course_id),
    FOREIGN KEY (teacher_id) REFERENCES teachers(teacher_id)
)";
$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS semester_enrollments (
    semester_enrollment_ID INT NOT NULL,
    student_id INT NOT NULL,
    semester_id INT NOT NULL,
    course INT NOT NULL CHECK (course >= 1 AND course <= 6),
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    enrollment_status_id INT NOT NULL,
    PRIMARY KEY (semester_enrollment_ID),
    UNIQUE (student_id, semester_id),
    FOREIGN KEY (student_id) REFERENCES Students(Student_ID),
    FOREIGN KEY (semester_id) REFERENCES semesters(semester_id),
    FOREIGN KEY (enrollment_status_id) REFERENCES enrollment_status(enrollment_status_id)
)";
$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS enrollment_subjects (
    enrollment_subject_id INT NOT NULL,
    semester_enrollment_id INT NOT NULL,
    open_course_id INT NOT NULL,
    PRIMARY KEY (enrollment_subject_id),
    UNIQUE (semester_enrollment_id, open_course_id),
    FOREIGN KEY (semester_enrollment_id) REFERENCES semester_enrollments(semester_enrollment_ID),
    FOREIGN KEY (open_course_id) REFERENCES open_courses(open_course_id)
)";
$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS grades (
    grade_id INT NOT NULL,
    enrollment_subject_id INT NOT NULL,
    grade DECIMAL(3, 2) NOT NULL CHECK (grade >= 2.00 AND grade <= 6.00),
    graded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    graded_by_teacher_id INT NOT NULL,
    PRIMARY KEY (grade_id),
    UNIQUE (enrollment_subject_id),
    FOREIGN KEY (enrollment_subject_id) REFERENCES enrollment_subjects(enrollment_subject_id),
    FOREIGN KEY (graded_by_teacher_id) REFERENCES teachers(teacher_id)
)";
$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS grade_history (
    grade_history_id INT NOT NULL,
    enrollment_subject_id INT NOT NULL,
    old_grade DECIMAL(3, 2) CHECK (old_grade IS NULL OR (old_grade >= 2.00 AND old_grade <= 6.00)),
    new_grade DECIMAL(3, 2) NOT NULL CHECK (new_grade >= 2.00 AND new_grade <= 6.00),
    action_id INT NOT NULL,
    changed_by_user_id INT NOT NULL,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    reason VARCHAR(30),
    PRIMARY KEY (grade_history_id),
    FOREIGN KEY (enrollment_subject_id) REFERENCES enrollment_subjects(enrollment_subject_id),
    FOREIGN KEY (action_id) REFERENCES action_grade_history(action_id),
    FOREIGN KEY (changed_by_user_id) REFERENCES Users(User_ID)
)";
$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS enrollment_history (
    enrollment_history_id INT NOT NULL,
    semester_enrollment_id INT NOT NULL,
    old_status_id INT NOT NULL,
    new_status_id INT NOT NULL,
    changed_by_user_id INT NOT NULL,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    reason VARCHAR(255),
    PRIMARY KEY (enrollment_history_id),
    FOREIGN KEY (semester_enrollment_id) REFERENCES semester_enrollments(semester_enrollment_ID),
    FOREIGN KEY (old_status_id) REFERENCES enrollment_status(enrollment_status_id),
    FOREIGN KEY (new_status_id) REFERENCES enrollment_status(enrollment_status_id),
    FOREIGN KEY (changed_by_user_id) REFERENCES Users(User_ID)
)";
$pdo->exec($sql);

$sql = "CREATE TABLE IF NOT EXISTS scholarship_applications (
    application_id INT NOT NULL,
    semester_enrollment_id INT NOT NULL,
    gpa DECIMAL(3, 2) NOT NULL CHECK (gpa >= 2.00 AND gpa <= 6.00),
    application_status_id INT NOT NULL,
    reviewer_user_id INT NULL,
    reviewer_comment VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    reviewed_at TIMESTAMP NULL,
    PRIMARY KEY (application_id),
    UNIQUE (semester_enrollment_id),
    FOREIGN KEY (semester_enrollment_id) REFERENCES semester_enrollments(semester_enrollment_ID),
    FOREIGN KEY (application_status_id) REFERENCES scholarship_application_status(application_status_id),
    FOREIGN KEY (reviewer_user_id) REFERENCES Users(User_ID)
)";
$pdo->exec($sql);

$stmt = $pdo->query("SELECT COUNT(*) FROM departments");
$count = $stmt->fetch(PDO::FETCH_NUM)[0];

if ($count == 0) {
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO user_roles (role_id, role_name) VALUES (:role_id, :role_name)");
        $stmt->execute(['role_id' => 1, 'role_name' => 'admin']);
        $stmt->execute(['role_id' => 2, 'role_name' => 'teacher']);
        $stmt->execute(['role_id' => 3, 'role_name' => 'student']);

        $defaultPassword = password_hash('password123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO Users (User_ID, Username, Password_hash, email, role_id) VALUES (:id, :username, :password, :email, :role_id)");
        $stmt->execute(['id' => 1, 'username' => 'admin',   'password' => $defaultPassword, 'email' => 'admin@tu-varna.bg',   'role_id' => 1]);
        $stmt->execute(['id' => 2, 'username' => 'ppetrov', 'password' => $defaultPassword, 'email' => 'petrov@tu-varna.bg',  'role_id' => 2]);
        $stmt->execute(['id' => 3, 'username' => 'mivanova','password' => $defaultPassword, 'email' => 'ivanova@tu-varna.bg', 'role_id' => 2]);
        $stmt->execute(['id' => 4, 'username' => 's2101',   'password' => $defaultPassword, 'email' => 's2101@tu-varna.bg',   'role_id' => 3]);
        $stmt->execute(['id' => 5, 'username' => 's2102',   'password' => $defaultPassword, 'email' => 's2102@tu-varna.bg',   'role_id' => 3]);

        $stmt = $pdo->prepare("INSERT INTO academic_titles (academic_title_id, academic_title_name) VALUES (:id, :name)");
        $stmt->execute(['id' => 1, 'name' => 'доц. д-р']);
        $stmt->execute(['id' => 2, 'name' => 'гл. ас. д-р']);

        $stmt = $pdo->prepare("INSERT INTO departments (department_ID, department_name) VALUES (:id, :name)");
        $stmt->execute(['id' => 1, 'name' => 'Софтуерно инженерство']);
        $stmt->execute(['id' => 2, 'name' => 'Компютърни системи']);
        $stmt->execute(['id' => 3, 'name' => 'Изкуствен интелект']);

        $stmt = $pdo->prepare("INSERT INTO specialties (specialty_ID, department_id, specialty_name) VALUES (:id, :department_id, :name)");
        $stmt->execute(['id' => 1, 'department_id' => 1, 'name' => 'Софтуерно инженерство']);
        $stmt->execute(['id' => 2, 'department_id' => 2, 'name' => 'Компютърни системи']);

        $stmt = $pdo->prepare("INSERT INTO teachers (teacher_id, user_id, department_id, teacher_name, phone_number, hire_date, academic_title_id, UCN) VALUES (:id, :user_id, :dept_id, :name, :phone, :hire_date, :title_id, :ucn)");
        $stmt->execute(['id' => 1, 'user_id' => 2, 'dept_id' => 1, 'name' => 'Петър Петров',  'phone' => '0888111222', 'hire_date' => '2018-09-01', 'title_id' => 1, 'ucn' => '8001011111']);
        $stmt->execute(['id' => 2, 'user_id' => 3, 'dept_id' => 2, 'name' => 'Мария Иванова', 'phone' => '0888333444', 'hire_date' => '2020-02-15', 'title_id' => 2, 'ucn' => '8502022222']);

        $stmt = $pdo->prepare("INSERT INTO Students (Student_ID, User_id, Specialty_ID, faculty_number, student_name, phone_number, UCN) VALUES (:id, :user_id, :specialty_id, :faculty_num, :name, :phone, :ucn)");
        $stmt->execute(['id' => 1, 'user_id' => 4, 'specialty_id' => 1, 'faculty_num' => '21621501', 'name' => 'Иван Георгиев',   'phone' => '0888555666', 'ucn' => '0341013333']);
        $stmt->execute(['id' => 2, 'user_id' => 5, 'specialty_id' => 1, 'faculty_num' => '21621502', 'name' => 'Елена Димитрова', 'phone' => '0888777888', 'ucn' => '0342024444']);

        $stmt = $pdo->prepare("INSERT INTO semesters (semester_id, semester_name) VALUES (:id, :name)");
        $stmt->execute(['id' => 1, 'name' => 'Зимен 2026/2027']);
        $stmt->execute(['id' => 2, 'name' => 'Летен 2027']);

        $stmt = $pdo->prepare("INSERT INTO subjects (subject_id, department_id, subject_name, credits) VALUES (:id, :dept_id, :name, :credits)");
        $stmt->execute(['id' => 1, 'dept_id' => 1, 'name' => 'Web приложения', 'credits' => 6]);
        $stmt->execute(['id' => 2, 'dept_id' => 1, 'name' => 'ООП',             'credits' => 5]);
        $stmt->execute(['id' => 3, 'dept_id' => 2, 'name' => 'Бази от данни',   'credits' => 6]);

        $stmt = $pdo->prepare("INSERT INTO open_courses (open_course_id, subject_id, semester_id) VALUES (:id, :subject_id, :semester_id)");
        $stmt->execute(['id' => 1, 'subject_id' => 1, 'semester_id' => 1]);
        $stmt->execute(['id' => 2, 'subject_id' => 2, 'semester_id' => 1]);
        $stmt->execute(['id' => 3, 'subject_id' => 3, 'semester_id' => 1]);

        $stmt = $pdo->prepare("INSERT INTO teacher_assignments (teacher_assignment_id, open_course_id, teacher_id) VALUES (:id, :open_course_id, :teacher_id)");
        $stmt->execute(['id' => 1, 'open_course_id' => 1, 'teacher_id' => 1]);
        $stmt->execute(['id' => 2, 'open_course_id' => 2, 'teacher_id' => 1]);
        $stmt->execute(['id' => 3, 'open_course_id' => 3, 'teacher_id' => 2]);

        $stmt = $pdo->prepare("INSERT INTO enrollment_status (enrollment_status_id, enrollment_status_name) VALUES (:id, :name)");
        $stmt->execute(['id' => 1, 'name' => 'Записан']);
        $stmt->execute(['id' => 2, 'name' => 'Заверил']);
        $stmt->execute(['id' => 3, 'name' => 'Прекъснал']);

        $stmt = $pdo->prepare("INSERT INTO semester_enrollments (semester_enrollment_ID, student_id, semester_id, course, enrollment_status_id) VALUES (:id, :student_id, :semester_id, :course, :status_id)");
        $stmt->execute(['id' => 1, 'student_id' => 1, 'semester_id' => 1, 'course' => 1, 'status_id' => 1]);
        $stmt->execute(['id' => 2, 'student_id' => 2, 'semester_id' => 1, 'course' => 1, 'status_id' => 1]);

        $stmt = $pdo->prepare("INSERT INTO enrollment_subjects (enrollment_subject_id, semester_enrollment_id, open_course_id) VALUES (:id, :enrollment_id, :course_id)");
        $stmt->execute(['id' => 1, 'enrollment_id' => 1, 'course_id' => 1]);
        $stmt->execute(['id' => 2, 'enrollment_id' => 1, 'course_id' => 2]);
        $stmt->execute(['id' => 3, 'enrollment_id' => 2, 'course_id' => 1]);

        $stmt = $pdo->prepare("INSERT INTO grades (grade_id, enrollment_subject_id, grade, graded_by_teacher_id) VALUES (:id, :subject_id, :grade, :teacher_id)");
        $stmt->execute(['id' => 1, 'subject_id' => 1, 'grade' => 5.50, 'teacher_id' => 1]);
        $stmt->execute(['id' => 2, 'subject_id' => 3, 'grade' => 6.00, 'teacher_id' => 1]);
        $stmt->execute(['id' => 3, 'subject_id' => 2,'grade' => 4.50, 'teacher_id' => 1]);

        $stmt = $pdo->prepare("INSERT INTO action_grade_history (action_id, action_name) VALUES (:id, :name)");
        $stmt->execute(['id' => 1, 'name' => 'Създадена оценка']);
        $stmt->execute(['id' => 2, 'name' => 'Актуализирана оценка']);
        $stmt->execute(['id' => 3, 'name' => 'Анулирана оценка']);

        $stmt = $pdo->prepare("INSERT INTO grade_history (grade_history_id, enrollment_subject_id, old_grade, new_grade, action_id, changed_by_user_id, reason) VALUES (:id, :enrollment_subject_id, :old_grade, :new_grade, :action_id, :user_id, :reason)");
        $stmt->execute(['id' => 1, 'enrollment_subject_id' => 1, 'old_grade' => null, 'new_grade' => 5.50, 'action_id' => 1, 'user_id' => 2, 'reason' => 'Първоначална оценка']);
        $stmt->execute(['id' => 2,'enrollment_subject_id' => 2,'old_grade' => null,'new_grade' => 4.50,'action_id' => 1,'user_id' => 2,'reason' => 'Първоначална оценка']);
        
        $stmt = $pdo->prepare("INSERT INTO enrollment_history (enrollment_history_id, semester_enrollment_id, old_status_id, new_status_id, changed_by_user_id, reason) VALUES (:id, :enrollment_id, :old_status, :new_status, :user_id, :reason)");
        $stmt->execute(['id' => 1, 'enrollment_id' => 1, 'old_status' => 1, 'new_status' => 1, 'user_id' => 1, 'reason' => 'Първоначално записване']);

        $stmt = $pdo->prepare("INSERT INTO scholarship_application_status (application_status_id, status_name) VALUES (:id, :name)");
        $stmt->execute(['id' => 1, 'name' => 'Чакаща']);
        $stmt->execute(['id' => 2, 'name' => 'Одобрена']);
        $stmt->execute(['id' => 3, 'name' => 'Отхвърлена']);

        $stmt = $pdo->prepare("INSERT INTO scholarship_applications (application_id,semester_enrollment_id, gpa, application_status_id, reviewer_user_id, reviewer_comment,created_at,reviewed_at) VALUES (:id, :semester_enrollment_id, :gpa, :application_status_id,:reviewer_user_id, :reviewer_comment, NOW(),:reviewed_at)");
        $stmt->execute(['id' => 1, 'semester_enrollment_id' => 1, 'gpa' => 5.00, 'application_status_id' => 2, 'reviewer_user_id' => 1,'reviewer_comment' => 'Одобрен поради добър академичен успех','reviewed_at' => date('Y-m-d H:i:s')]);
        $stmt->execute(['id' => 2, 'semester_enrollment_id' => 2,'gpa' => 6.00,'application_status_id' => 1, 'reviewer_user_id' => null,'reviewer_comment' => null,'reviewed_at' => null]);

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Грешка: " . $e->getMessage();
    }
}

$pdo = null;
?>