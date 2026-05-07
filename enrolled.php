<?php
session_start();
require 'config.php'; // Uses PDO now

if(!isset($_SESSION['user_id'])) header("Location: login.php");
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'Learner';

$conn = connectDatabase();

// Handle payment slip upload
if(isset($_POST['upload_payment']) && isset($_FILES['payment_slip']) && isset($_POST['course_id'])){
    $course_id = intval($_POST['course_id']);
    $file = $_FILES['payment_slip'];

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $allowed = ['jpg','jpeg','png','pdf'];
    if(in_array(strtolower($ext), $allowed)){
        if(!is_dir('slips')) mkdir('slips',0777,true);
        $newName = "slips/".time()."_".$user_id.".".$ext;
        move_uploaded_file($file['tmp_name'],$newName);

        $stmt = $conn->prepare("UPDATE course_enrollments SET payment_slip=?, payment_status='pending', payment_time=NOW() WHERE user_id=? AND course_id=?");
        $stmt->execute([$newName, $user_id, $course_id]);
        $msg = "✅ Payment slip uploaded successfully! Admin will confirm within 24 hours.";
    } else {
        $msg = "❌ Invalid file type. Allowed: jpg, jpeg, png, pdf.";
    }
}

// Handle new enrollment initiation
if(isset($_POST['enroll_course'])){
    $course_id = intval($_POST['enroll_course']);

    $stmt = $conn->prepare("SELECT * FROM course_enrollments WHERE user_id=? AND course_id=?");
    $stmt->execute([$user_id, $course_id]);
    $res = $stmt->fetchAll();

    if(count($res) > 0){
        $msg = "⚠️ You already enrolled or payment is pending for this course.";
    } else {
        $stmt = $conn->prepare("INSERT INTO course_enrollments(user_id, course_id, payment_status) VALUES(?, ?, 'pending')");
        $stmt->execute([$user_id, $course_id]);
        $msg = "✅ Enrollment initiated. Please pay via UPI and upload payment slip below.";
    }
}

// Fetch all courses
$stmt = $conn->query("SELECT * FROM courses");
$courses = $stmt->fetchAll();

// Fetch user's enrolled courses
$stmt = $conn->prepare("SELECT c.id,c.title,c.desc,e.payment_status,e.payment_slip,e.access_granted
        FROM courses c
        JOIN course_enrollments e ON c.id=e.course_id
        WHERE e.user_id=?");
$stmt->execute([$user_id]);
$enrolled = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RiseGen | My Courses</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen text-gray-900">

<div class="max-w-7xl mx-auto py-10 px-4">

    <h1 class="text-3xl font-bold mb-8 text-black">Welcome, <?= htmlspecialchars($username) ?></h1>

    <?php if(isset($msg)): ?>
        <div class="bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 p-4 mb-8 rounded shadow"><?= $msg ?></div>
    <?php endif; ?>

    <!-- Available Courses -->
    <h2 class="text-2xl font-semibold mb-6 text-black">Available Courses</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
        <?php foreach($courses as $c): ?>
        <div class="bg-white rounded-xl shadow p-6 flex flex-col justify-between hover:shadow-lg transition">
            <div>
                <h3 class="font-bold text-lg mb-3 text-black"><?= htmlspecialchars($c['title']) ?></h3>
                <p class="text-gray-600 text-sm mb-4"><?= htmlspecialchars($c['desc']) ?></p>
            </div>
            <form method="POST">
                <input type="hidden" name="enroll_course" value="<?= $c['id'] ?>">
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 w-full font-medium">Enroll / Pay</button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Enrolled Courses -->
    <?php if(!empty($enrolled)): ?>
    <h2 class="text-2xl font-semibold mb-6 text-black">Your Enrolled Courses</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach($enrolled as $e): ?>
        <div class="bg-gray-50 rounded-xl shadow p-6 flex flex-col justify-between hover:shadow-lg transition">
            <h3 class="font-bold text-lg mb-3 text-black"><?= htmlspecialchars($e['title']) ?></h3>
            <p class="text-gray-700 text-sm mb-1">Payment Status: <span class="<?= $e['payment_status']=='confirmed'?'text-green-600':'text-yellow-600' ?>"><?= ucfirst($e['payment_status']) ?></span></p>
            <p class="text-gray-700 text-sm mb-3">Access: <?= $e['access_granted'] ? "<span class='text-green-600'>Granted</span>" : "<span class='text-red-600'>Pending</span>" ?></p>

            <?php if($e['payment_status']=='pending'): ?>
            <div class="mb-3 text-sm text-gray-800">
                <p>1️⃣ Pay via UPI: <strong>xyz@kotak</strong></p>
                <p>2️⃣ Send your payment slip to <strong>78xyz</strong>.</p>
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="course_id" value="<?= $e['id'] ?>">
                <input type="file" name="payment_slip" accept="image/*,application/pdf" required class="mb-3 w-full rounded border border-gray-400 p-2 bg-white">
                <button type="submit" name="upload_payment" class="bg-blue-600 text-white px-4 py-2 rounded-lg w-full hover:bg-blue-700 font-medium">Upload Slip</button>
            </form>
            <?php endif; ?>

            <?php if($e['payment_slip']): ?>
                <a href="<?= $e['payment_slip'] ?>" target="_blank" class="text-blue-700 underline text-sm mt-2">View Uploaded Slip</a>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>
</body>
</html>
