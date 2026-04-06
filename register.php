<?php
/**
 * register.php
 * Handles Student Event Registration form submission
 * Stores data in MySQL via phpMyAdmin
 *
 * SETUP INSTRUCTIONS:
 * 1. Import student_events.sql into phpMyAdmin to create the database & table
 * 2. Update DB credentials below
 * 3. Place this file in the same folder as student_event_registration.html
 * 4. Upload folder to a PHP-enabled server (XAMPP / WAMP / live host)
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ── DB Configuration ──────────────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_USER', 'root');          // Change to your phpMyAdmin username
define('DB_PASS', '');              // Change to your phpMyAdmin password
define('DB_NAME', 'student_events');
define('UPLOAD_DIR', 'uploads/');   // Folder to store uploaded ID proofs
// ─────────────────────────────────────────────────────────────────

header('Content-Type: text/plain; charset=utf-8');

// Allow only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Method Not Allowed');
}

// ── Helper: sanitize input ────────────────────────────────────────
function clean(string $val): string {
    return htmlspecialchars(strip_tags(trim($val)));
}

// ── Collect & sanitize form data ──────────────────────────────────
$first_name        = clean($_POST['first_name']        ?? '');
$last_name         = clean($_POST['last_name']         ?? '');
$email             = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
$phone             = clean($_POST['phone']             ?? '');
$dob               = clean($_POST['dob']               ?? '');
$gender            = clean($_POST['gender']            ?? '');
$student_id        = clean($_POST['student_id']        ?? '');
$department        = clean($_POST['department']        ?? '');
$year_sem          = clean($_POST['year_sem']          ?? '');
$cgpa              = clean($_POST['cgpa']              ?? '');
$advisor           = clean($_POST['advisor']           ?? '');
$event_name        = clean($_POST['event_name']        ?? '');
$event_date        = clean($_POST['event_date']        ?? '');
$participation_type = clean($_POST['participation_type'] ?? '');
$team_name         = clean($_POST['team_name']         ?? '');
$interests         = clean($_POST['interests']         ?? '');
$remarks           = clean($_POST['remarks']           ?? '');

// ── Validation ────────────────────────────────────────────────────
$errors = [];

if (empty($first_name))          $errors[] = 'First name is required.';
if (empty($last_name))           $errors[] = 'Last name is required.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
if (empty($student_id))          $errors[] = 'Student ID is required.';
if (empty($department))          $errors[] = 'Department is required.';
if (empty($year_sem))            $errors[] = 'Year/Semester is required.';
if (empty($event_name))          $errors[] = 'Event name is required.';
if (empty($event_date))          $errors[] = 'Event date is required.';
if (empty($participation_type))  $errors[] = 'Participation type is required.';
// if (!isset($_POST['declaration'])) $errors[] = 'You must accept the declaration.';

if (!empty($errors)) {
    http_response_code(422);
    die('ERROR: ' . implode(' | ', $errors));
}

// ── Handle file upload ────────────────────────────────────────────
$id_proof_path = '';

if (!empty($_FILES['id_proof']['name'])) {
    $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
    $max_size      = 2 * 1024 * 1024; // 2 MB

    $file_type = mime_content_type($_FILES['id_proof']['tmp_name']);
    $file_size = $_FILES['id_proof']['size'];

    if (!in_array($file_type, $allowed_types)) {
        http_response_code(422);
        die('ERROR: Only JPG, PNG, or PDF files are allowed.');
    }
    if ($file_size > $max_size) {
        http_response_code(422);
        die('ERROR: File size must not exceed 2 MB.');
    }

    // Create upload dir if it doesn't exist
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }

    $ext           = pathinfo($_FILES['id_proof']['name'], PATHINFO_EXTENSION);
    $safe_filename = preg_replace('/[^a-z0-9_]/i', '', $student_id) . '_' . time() . '.' . $ext;
    $dest          = UPLOAD_DIR . $safe_filename;

    if (!move_uploaded_file($_FILES['id_proof']['tmp_name'], $dest)) {
        http_response_code(500);
        die('ERROR: File upload failed.');
    }

    $id_proof_path = $dest;
}

// ── DB Connection ─────────────────────────────────────────────────
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    http_response_code(500);
    die('ERROR: DB connection failed – ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');

// ── Prepared Statement ────────────────────────────────────────────
$sql = "INSERT INTO registrations
    (first_name, last_name, email, phone, dob, gender,
     student_id, department, year_sem, cgpa, advisor,
     event_name, event_date, participation_type, team_name,
     interests, remarks, id_proof_path, registered_at)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    die('ERROR: Prepare failed – ' . $conn->error);
}

$stmt->bind_param(
    'ssssssssssssssssss',
    $first_name, $last_name, $email, $phone, $dob, $gender,
    $student_id, $department, $year_sem, $cgpa, $advisor,
    $event_name, $event_date, $participation_type, $team_name,
    $interests, $remarks, $id_proof_path
);

if (!$stmt->execute()) {
    http_response_code(500);
    die('ERROR: Insert failed – ' . $stmt->error);
}

$inserted_id = $stmt->insert_id;
$stmt->close();
$conn->close();

// ── Optional: Send confirmation email ────────────────────────────
// Uncomment and configure SMTP (e.g. PHPMailer) for production use.
// mail($email, "Registration Confirmed – $event_name",
//     "Hi $first_name,\n\nYour registration (ID #$inserted_id) for $event_name on $event_date is confirmed.\n\nSee you there!");

echo "SUCCESS: Registration #$inserted_id saved successfully.";
?>
