<?php
/**
 * RiseGen Certificate Generator - Master Version
 * Combines high-end styling with automated workflow and navigation.
 */

// 1. COLLECT DATA FROM THE URL
$studentName = isset($_GET["name"])    ? htmlspecialchars($_GET["name"]) : "Learner";
$certID      = isset($_GET["cert_id"]) ? htmlspecialchars($_GET["cert_id"]) : "N/A";
$userScore   = isset($_GET["score"])   ? (int)$_GET["score"] : 0;

// 2. GENERATE DYNAMIC TIMESTAMP
$dateIssued = date("F j, Y");
$timeIssued = date("g:i A");

// 3. THE GATEKEEPER: Secure the page (70% Threshold)
$passingScore = 70;

if ($userScore < $passingScore) {
    echo "
    <div style='text-align:center; padding:100px; font-family:sans-serif; background:#020617; color:#f1f5f9; height:100vh; display:flex; flex-direction:column; justify-content:center; align-items:center;'>
        <h1 style='color:#ef4444; font-size:3rem;'>⚠ Access Denied</h1>
        <p style='font-size:1.2rem;'>A minimum score of $passingScore% is required to generate a certificate.</p>
        <p>Your Score: <strong>$userScore%</strong></p>
        <br>
        <a href='index.php' style='color:#3b82f6; text-decoration:none; border:1px solid #3b82f6; padding:10px 20px; border-radius:8px;'>Return to Entrance Portal</a>
    </div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RiseGen Certificate - <?php echo $studentName; ?></title>
    <style>
        /* Base Styling */
        body { 
            background-color: #0f172a; 
            margin: 0; 
            display: flex; 
            flex-direction: column;
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            font-family: 'Georgia', serif; 
        }
        
        /* Navigation Controls (Visible only on screen) */
        .controls {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 12px;
            z-index: 100;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            border: none;
            text-decoration: none;
            font-family: sans-serif;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px rgba(0,0,0,0.3);
        }

        .btn-print { background: #3b82f6; color: white; }
        .btn-exit { background: #ef4444; color: white; }
        .btn:hover { opacity: 0.9; transform: translateY(-2px); }

        /* Certificate Container */
        .certificate-container {
            background: #fff;
            width: 950px;
            height: 650px;
            padding: 60px;
            border: 30px solid #1e293b; 
            position: relative;
            box-sizing: border-box;
            text-align: center;
            box-shadow: 0 40px 60px rgba(0,0,0,0.5);
        }

        /* Gold Ornamental Inner Border */
        .certificate-container::after {
            content: '';
            position: absolute;
            top: 15px; left: 15px; right: 15px; bottom: 15px;
            border: 4px double #d4af37;
            pointer-events: none;
        }

        .main-title { 
            font-size: 70px; 
            color: #1e293b; 
            margin: 0; 
            text-transform: uppercase; 
            letter-spacing: 4px;
        }

        .sub-title { 
            font-size: 22px; 
            font-style: italic; 
            color: #64748b; 
            margin-bottom: 40px; 
        }
        
        .name-display {
            font-size: 55px;
            color: #3b82f6; 
            border-bottom: 2px solid #e2e8f0;
            display: inline-block;
            margin: 15px 0;
            padding: 0 50px;
            font-weight: bold;
        }

        .description { 
            font-size: 20px; 
            color: #334155; 
            width: 85%; 
            margin: 0 auto; 
            line-height: 1.6; 
        }

        .footer-info {
            margin-top: 70px;
            display: flex;
            justify-content: space-between;
            padding: 0 40px;
            align-items: flex-end;
        }

        .info-block { 
            border-top: 2px solid #1e293b; 
            width: 250px; 
            padding-top: 15px; 
            font-size: 14px; 
            text-align: left;
        }

        .stamp { 
            font-size: 35px; 
            color: #10b981; 
            font-weight: bold; 
            transform: rotate(-10deg); 
            border: 5px double #10b981; 
            padding: 5px 20px; 
            display: inline-block;
            opacity: 0.8;
            margin-bottom: 10px;
        }

        /* Print Logic */
        @media print {
            .controls { display: none; }
            body { background: white; padding: 0; }
            .certificate-container { 
                box-shadow: none; 
                border: 30px solid #1e293b !important;
                margin: 0;
            }
        }
    </style>
</head>
<body onload="handleAutoPrint()">

    <div class="controls">
        <button class="btn btn-print" onclick="window.print()">Download Certificate (PDF)</button>
        <a href="index.php" class="btn btn-exit">Close & Exit Portal</a>
    </div>

    <div class="certificate-container">
        <div class="main-title">Achievement</div>
        <div class="sub-title">This certificate is proudly presented to</div>

        <div class="name-display"><?php echo $studentName; ?></div>

        <p class="description">
            For outstanding performance and achieving a mastery score of 
            <strong><?php echo $userScore; ?>%</strong> in the <strong>RiseGen Entrance Examination</strong>. 
            This document verifies technical competency and successful admission requirements.
        </p>

        <div class="footer-info">
            <div class="info-block">
                <strong>VERIFICATION ID</strong><br>
                <span style="font-family: monospace; font-weight: bold;"><?php echo $certID; ?></span>
            </div>

            <div class="stamp">VERIFIED</div>

            <div class="info-block" style="text-align: right;">
                <strong>DATE OF ISSUANCE</strong><br>
                <?php echo $dateIssued; ?><br><?php echo $timeIssued; ?>
            </div>
        </div>
    </div>

    <script>
        function handleAutoPrint() {
            // Trigger print dialog after a slight delay to ensure CSS loads
            setTimeout(() => {
                window.print();
            }, 1200);
        }
    </script>
</body>
</html>