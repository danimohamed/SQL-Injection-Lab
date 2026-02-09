<?php
// ============================================================
// index.php - Main Menu / Landing Page
// SQL Injection Educational Lab
// FOR EDUCATIONAL PURPOSES ONLY - localhost only
// ============================================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL Injection Lab - Educational</title>
    <style>
        /* ---- Global Styles ---- */
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0f0f1a;
            color: #e0e0e0;
            min-height: 100vh;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        /* ---- Header ---- */
        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header img.logo {
            width: 300px;
            margin-bottom: 16px;
        }

        .header h1 {
            font-size: 2rem;
            color: #00e5ff;
            margin-bottom: 8px;
        }

        .header p {
            color: #ff5252;
            font-weight: bold;
            font-size: 0.95rem;
        }

        .warning-banner {
            background: #1a1a2e;
            border: 2px solid #ff5252;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 36px;
            text-align: center;
            color: #ff8a80;
            line-height: 1.6;
        }

        /* ---- Card Grid ---- */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 20px;
        }

        .card {
            background: #1a1a2e;
            border: 1px solid #2a2a4a;
            border-radius: 10px;
            padding: 24px;
            transition: transform 0.2s, border-color 0.2s;
        }

        .card:hover {
            transform: translateY(-4px);
            border-color: #00e5ff;
        }

        .card.secure {
            border-color: #00c853;
        }

        .card.secure:hover {
            border-color: #69f0ae;
        }

        .card h2 {
            font-size: 1.15rem;
            margin-bottom: 6px;
        }

        .card .tag {
            display: inline-block;
            font-size: 0.7rem;
            padding: 2px 8px;
            border-radius: 4px;
            margin-bottom: 12px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .tag.vuln   { background: #b71c1c; color: #fff; }
        .tag.safe   { background: #1b5e20; color: #fff; }

        .card p {
            font-size: 0.88rem;
            color: #aaa;
            line-height: 1.5;
            margin-bottom: 16px;
        }

        .card code {
            display: block;
            background: #0d0d1a;
            padding: 8px 10px;
            border-radius: 4px;
            font-size: 0.82rem;
            color: #ff8a80;
            margin-bottom: 16px;
            word-break: break-all;
        }

        .card a.btn {
            display: inline-block;
            padding: 8px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            font-size: 0.85rem;
            transition: background 0.2s;
        }

        .btn.red   { background: #c62828; color: #fff; }
        .btn.red:hover { background: #e53935; }
        .btn.green { background: #2e7d32; color: #fff; }
        .btn.green:hover { background: #43a047; }

        /* ---- Footer ---- */
        .footer {
            text-align: center;
            margin-top: 48px;
            padding: 20px 0;
            border-top: 1px solid #2a2a4a;
            color: #666;
            font-size: 0.85rem;
        }

        .footer strong {
            color: #00e5ff;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <img class="logo" src="https://student.emsi.ma/app_logo_emsi.png" alt="EMSI Logo">
            <h1>SQL Injection Lab</h1>
        </div>

        <!-- Course Info Banner -->
        <div class="warning-banner">
            Ce lab a &eacute;t&eacute; cr&eacute;&eacute; pour le module
            <strong>S&eacute;curit&eacute; des Applications</strong> &mdash; EMSI<br>
            
        </div>

        <!-- Lab Cards -->
        <div class="grid">

            <!-- 1. Vulnerable Login -->
            <div class="card">
                <h2>1. Login Bypass</h2>
                <span class="tag vuln">Vulnerable</span>
                <p>Authentication bypass via string concatenation. The attacker can log in without knowing the password.</p>
                <code>admin' --</code>
                <a class="btn red" href="login_vulnerable.php">Open Lab</a>
            </div>

            <!-- 2. Data Extraction (OR 1=1) + UNION -->
            <div class="card">
                <h2>2. Data Extraction</h2>
                <span class="tag vuln">Vulnerable</span>
                <p>Dump all rows with OR 1=1, and extract specific columns with UNION SELECT.</p>
                <code>' OR '1'='1</code>
                <a class="btn red" href="read_vulnerable.php">Open Lab</a>
            </div>

            <!-- 3. Update Injection -->
            <div class="card">
                <h2>3. Privilege Escalation</h2>
                <span class="tag vuln">Vulnerable</span>
                <p>Modify a user's role from "user" to "admin" through an UPDATE injection.</p>
                <code>user1', role='admin' WHERE username='user1' --</code>
                <a class="btn red" href="update_vulnerable.php">Open Lab</a>
            </div>

            <!-- 4. Delete Injection -->
            <div class="card">
                <h2>4. Delete Injection</h2>
                <span class="tag vuln">Vulnerable</span>
                <p>Delete arbitrary rows or wipe the entire table through a DELETE injection.</p>
                <code>' OR '1'='1</code>
                <a class="btn red" href="delete_vulnerable.php">Open Lab</a>
            </div>

            <!-- 5. Blind SQL Injection -->
            <div class="card">
                <h2>5. Blind SQL Injection</h2>
                <span class="tag vuln">Vulnerable</span>
                <p>Extract data one character at a time using true/false responses and SUBSTRING().</p>
                <code>admin' AND SUBSTRING(password,1,1)='a' --</code>
                <a class="btn red" href="blind_vulnerable.php">Open Lab</a>
            </div>

            <!-- 6. Secure Login -->
            <div class="card secure">
                <h2>6. Secure Login</h2>
                <span class="tag safe">Secure</span>
                <p>Same login form re-implemented with PDO prepared statements. All injection attempts fail.</p>
                <code>Prepared statements + bound parameters</code>
                <a class="btn green" href="login_secure.php">Open Lab</a>
            </div>

        </div><!-- /grid -->

        <!-- Footer -->
        <div class="footer">
            Developed by <strong>Mohamed Dani</strong>
        </div>

    </div><!-- /container -->
</body>
</html>
