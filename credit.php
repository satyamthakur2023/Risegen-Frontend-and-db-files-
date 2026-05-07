<?php
// Turn off error reporting for a clean certificate, or E_ALL to debug
error_reporting(0); 

// 1. Get data from URL parameters
$studentName = isset($_GET["name"]) ? htmlspecialchars($_GET["name"]) : "Learner";
$certID      = isset($_GET["cert_id"]) ? htmlspecialchars($_GET["cert_id"]) : "N/A";
$userScore   = isset($_GET["score"]) ? intval($_GET["score"]) : 0;

// 2. THE GATEKEEPER: This is why it might be "not working"
// If you are testing manually, make sure your URL has &score=75 at the end
if ($userScore < 70) {
    die("<div style='text-align:center; margin-top:50px; font-family:sans-serif;'>
            <h1 style='color:red;'>Access Denied</h1>
            <p>Score threshold (70%) not met. Current score detected: $userScore%</p>
            <a href='index.php'>Return to Test</a>
         </div>");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RiseGen Certificate | <?php echo $studentName; ?></title>
    <style>
        body { background: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; font-family: 'Georgia', serif; margin:0; }
        .cert { background: white; width: 850px; height: 550px; padding: 50px; border: 20px solid #1e293b; position: relative; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.2); box-sizing: border-box; }
        .cert::after { content: ''; position: absolute; top: 10px; left: 10px; right: 10px; bottom: 10px; border: 2px solid #d4af37; pointer-events: none; }
        h1 { font-size: 50px; color: #1e293b; margin: 10px 0; }
        .name { font-size: 40px; color: #3b82f6; border-bottom: 2px solid #eee; display: inline-block; padding: 0 30px; margin: 20px 0; }
        .footer { margin-top: 50px; display: flex; justify-content: space-around; font-size: 14px; }
        .controls { position: fixed; top: 20px; right: 20px; display: flex; gap: 10px; z-index: 100; }
        .btn { padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; text-decoration: none; font-family: sans-serif; }
        .btn-print { background: #27ae60; color: white; }
        .btn-exit { background: #e74c3c; color: white; }
        @media print { .controls { display: none; } body { background: white; } .cert { box-shadow: none; border: 20px solid #1e293b !important; } }
    </style>
</head>
<body onload="handleAutoPrint()">

    <div class="controls">
        <button class="btn btn-print" onclick="window.print()">Download PDF</button>
        <a href="index.php" class="btn btn-exit">✖ Close & Exit</a>
    </div>

    <div class="cert">
        <p style="color:#64748b; font-weight:bold; letter-spacing:2px;">OFFICIAL COMPLETION</p>
        <h1>CERTIFICATE</h1>
        <p>This recognizes that</p>
        <div class="name"><?php echo $studentName; ?></div>
        <p>passed the entrance exam with a score of <strong><?php echo $userScore; ?>%</strong>.</p>
        <div class="footer">
            <div><strong>VERIFICATION ID:</strong><br><?php echo $certID; ?></div>
            <div><strong>ISSUE DATE:</strong><br><?php echo date("M d, Y"); ?></div>
        </div>
    </div>

    <script>
        function handleAutoPrint() {
            setTimeout(() => { window.print(); }, 1000);
        }
    </script>
</body>
</html>