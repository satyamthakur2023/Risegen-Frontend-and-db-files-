<?php
// ===================================================
// Admin Registration - RiseGen
// ===================================================
session_start();
require_once 'config.php'; // Make sure this contains connectDatabase()

$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // --- Basic Validation ---
    if (!$username || !$email || !$password || !$confirm_password) {
        $message = "<span class='error'>❌ Fill all fields.</span>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<span class='error'>❌ Invalid email address.</span>";
    } elseif (strlen($password) < 8) {
        $message = "<span class='error'>❌ Password must be 8+ characters.</span>";
    } elseif ($password !== $confirm_password) {
        $message = "<span class='error'>❌ Passwords do not match.</span>";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $pdo = connectDatabase();

            // --- Check for duplicates ---
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username = :username OR email = :email");
            $stmt->execute([':username' => $username, ':email' => $email]);
            
            if ($stmt->fetchColumn() > 0) {
                $message = "<span class='error'>❌ Username or Email already exists.</span>";
            } else {
                // --- Insert new admin ---
                $stmt = $pdo->prepare("INSERT INTO admins (username, email, password_hash) VALUES (:username, :email, :password_hash)");
                $stmt->execute([
                    ':username' => $username,
                    ':email' => $email,
                    ':password_hash' => $password_hash
                ]);

                $message = "<span class='success'>✅ Admin registered successfully! <a href='admin_login.php'>Login here</a>.</span>";
            }

        } catch (PDOException $e) {
            error_log("Admin Registration Error: " . $e->getMessage());
            $message = "<span class='error'>❌ Database error. Try again later.</span>";
        }
    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Register - RiseGen</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<style>
/* ---------- General ---------- */
body {
  font-family: 'Poppins', sans-serif;
  margin: 0;
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #0d1b2a, #1b263b, #415a77);
  overflow: hidden;
}

.container {
  background: rgba(255,255,255,0.08);
  border-radius: 20px;
  padding: 50px 40px;
  width: 400px;
  backdrop-filter: blur(12px);
  box-shadow: 0 8px 30px rgba(0,255,255,0.2);
  text-align: center;
  transition: 0.3s ease-in-out;
}
.container:hover {
  box-shadow: 0 12px 40px rgba(0,255,255,0.4);
}

h2 {
  margin-bottom: 30px;
  font-weight: 700;
  color: #00d4ff;
  text-shadow: 0 0 10px rgba(0,212,255,0.6);
}

/* ---------- Input Groups ---------- */
.input-group {
  position: relative;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  background: rgba(255,255,255,0.1);
  border-radius: 10px;
  padding: 10px 12px;
  transition: 0.3s;
}
.input-group:hover {
  background: rgba(255,255,255,0.15);
  box-shadow: 0 0 10px rgba(0,212,255,0.3);
}

.input-group i:first-child {
  color: #00d4ff;
  font-size: 1.2em;
  margin-right: 10px;
}

.input-group input {
  border: none;
  outline: none;
  flex: 1;
  background: transparent;
  color: #fff;
  font-size: 1em;
  padding: 10px 5px;
}

.input-group input::placeholder {
  color: rgba(255,255,255,0.6);
}

.input-group .toggle-pass {
  color: #00d4ff;
  cursor: pointer;
  font-size: 1.2em;
}

/* ---------- Button ---------- */
button {
  width: 100%;
  padding: 14px;
  border: none;
  border-radius: 10px;
  background: linear-gradient(90deg, #00d4ff, #007bff);
  color: #fff;
  font-size: 1em;
  font-weight: 600;
  cursor: pointer;
  transition: 0.4s;
  box-shadow: 0 5px 15px rgba(0,212,255,0.3);
}
button:hover {
  background-position: right;
  box-shadow: 0 5px 25px rgba(0,212,255,0.6);
  transform: translateY(-2px);
}

/* ---------- Messages ---------- */
.message { margin-bottom: 20px; font-weight: 500; }
.error { color: #ff4c4c; }
.success { color: #00ffae; }

a { color: #00d4ff; text-decoration: none; font-weight: 600; }
a:hover { text-decoration: underline; }

</style>
</head>
<body>
<div class="container">
  <h2>Admin Registration</h2>
  <div class="message"><?= $message ?></div>
  <form method="POST" autocomplete="off">
    <div class="input-group"><i>👤</i><input type="text" name="username" placeholder="Username" required></div>
    <div class="input-group"><i>📧</i><input type="email" name="email" placeholder="Email" required></div>
    
    <div class="input-group">
      <i>🔒</i>
      <input type="password" name="password" placeholder="Password" id="password" required>
      <i class="toggle-pass">👁️</i>
    </div>

    <div class="input-group">
      <i>🔒</i>
      <input type="password" name="confirm_password" placeholder="Confirm Password" id="confirm_password" required>
      <i class="toggle-pass">👁️</i>
    </div>

    <button type="submit">Register</button>
  </form>
  <p>Back To <a href="admin_dashboard.php">Dashboard</a></p>
</div>

<script>
const togglePassIcons = document.querySelectorAll('.toggle-pass');
togglePassIcons.forEach((icon, index) => {
    icon.addEventListener('click', () => {
        const input = index === 0 ? document.getElementById('password') : document.getElementById('confirm_password');
        input.type = input.type === 'password' ? 'text' : 'password';
        icon.textContent = input.type === 'password' ? '👁️' : '🙈';
    });
});
</script>
</body>
</html>
