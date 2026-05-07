<?php
session_start();
require_once 'config.php'; // Make sure connectDatabase() exists here

$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $message = "<span class='error'>❌ Fill all fields.</span>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<span class='error'>❌ Invalid email.</span>";
    } else {
        try {
            $pdo = connectDatabase();

            // Correct column names
            $stmt = $pdo->prepare("SELECT admin_id, username, password_hash FROM admins WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin && password_verify($password, $admin['password_hash'])) {
                // Login successful
                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['admin_name'] = $admin['username'];

                // Optional: Update last_login timestamp
                $update = $pdo->prepare("UPDATE admins SET last_login = NOW() WHERE admin_id = :id");
                $update->execute([':id' => $admin['admin_id']]);

                header("Location: admin_dashboard.php");
                exit;
            } else {
                $message = "<span class='error'>❌ Invalid email or password.</span>";
            }

        } catch (PDOException $e) {
            error_log("Admin Login Error: " . $e->getMessage());
            $message = "<span class='error'>❌ Database error. Try again later.</span>";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Login - RiseGen</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body {
  font-family: 'Poppins', sans-serif;
  margin: 0;
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg,#0d1b2a,#1b263b,#415a77);
  color: #fff;
}
.container {
  background: rgba(255,255,255,0.08);
  border-radius: 20px;
  padding: 50px 40px;
  width: 380px;
  backdrop-filter: blur(12px);
  box-shadow: 0 8px 30px rgba(0,255,255,0.2);
  text-align: center;
  transition: 0.3s ease-in-out;
}
.container:hover { box-shadow: 0 12px 40px rgba(0,255,255,0.4); }
h2 { margin-bottom: 25px; font-weight: 700; color: #00d4ff; text-shadow: 0 0 10px rgba(0,212,255,0.6); }
.input-group {
  position: relative;
  display: flex;
  align-items: center;
  background: rgba(255,255,255,0.1);
  border-radius: 10px;
  margin-bottom: 20px;
  padding: 10px 12px;
  transition: 0.3s;
}
.input-group:hover { background: rgba(255,255,255,0.15); box-shadow: 0 0 10px rgba(0,212,255,0.3); }
.input-group i:first-child { color: #00d4ff; font-size: 1.2em; margin-right: 10px; }
.input-group input { flex:1; border:none; outline:none; background:transparent; color:#fff; font-size:1em; padding:10px 5px;}
.input-group input::placeholder { color: rgba(255,255,255,0.6);}
.input-group .toggle-pass { color:#00d4ff; cursor:pointer; font-size:1.2em; padding-left:10px;}
button {
  width: 100%; padding: 14px; border:none; border-radius:10px;
  background: linear-gradient(90deg,#00d4ff,#007bff); color:#fff;
  font-size:1em; font-weight:600; cursor:pointer; transition:0.4s;
  box-shadow:0 5px 15px rgba(0,212,255,0.3);
}
button:hover { box-shadow:0 5px 25px rgba(0,212,255,0.6); transform:translateY(-2px);}
.message { margin-bottom: 20px; font-weight:500;}
.error { color:#ff4c4c;}
.success { color:#00ffae;}
a { color:#00d4ff; text-decoration:none; font-weight:600;}
a:hover { text-decoration:underline;}
</style>
</head>
<body>
<div class="container">
  <h2>Admin Login</h2>
  <div class="message"><?= $message ?></div>
  <form method="POST" autocomplete="off">
    <div class="input-group"><i>📧</i><input type="email" name="email" placeholder="Email" required></div>
    <div class="input-group">
      <i>🔒</i>
      <input type="password" name="password" id="password" placeholder="Password" required autocomplete="current-password">
      <i class="toggle-pass">👁️</i>
    </div>
    <button type="submit">Login</button>
  </form>
  <p>New admin? <a href="admin_register.php">Register</a></p>
</div>

<script>
document.querySelectorAll('.toggle-pass').forEach(icon => {
    icon.addEventListener('click', () => {
        const input = icon.previousElementSibling;
        input.type = input.type === 'password' ? 'text' : 'password';
        icon.textContent = input.type === 'password' ? '👁️' : '🙈';
    });
});
</script>
</body>
</html>
