<?php
// ============================================================
// login_vulnerable.php - Authentication Bypass Demo
// SQL Injection Educational Lab
// FOR EDUCATIONAL PURPOSES ONLY - localhost only
// ============================================================
//
// VULNERABILITY: String concatenation in SQL query
// -----------------------------------------------
// The query is built by directly embedding user input into the
// SQL string.  An attacker can manipulate the query structure.
//
// Example payload (username field):
//   admin' --
//
// This turns the query into:
//   SELECT * FROM users WHERE username='admin' -- ' AND password='anything'
//
// Everything after -- is a SQL comment, so the password check
// is completely bypassed.
// ============================================================

require_once 'db.php';

$result  = null;
$error   = null;
$query   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // =====================================================
    // VULNERABLE: Direct string concatenation
    // NEVER do this in real applications!
    // =====================================================
    $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";

    try {
        $pdo  = getDB();
        $stmt = $pdo->query($query);
        $result = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vulnerable Login - SQL Injection Lab</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #0f0f1a; color: #e0e0e0; min-height: 100vh; }
        .container { max-width: 700px; margin: 0 auto; padding: 40px 20px; }
        a.back { color: #00e5ff; text-decoration: none; font-size: 0.9rem; }
        a.back:hover { text-decoration: underline; }
        h1 { color: #ff5252; margin: 16px 0 4px; }
        .tag { display: inline-block; background: #b71c1c; color: #fff; font-size: 0.7rem; padding: 2px 8px; border-radius: 4px; text-transform: uppercase; font-weight: bold; margin-bottom: 16px; }
        .info-box { background: #1a1a2e; border: 1px solid #2a2a4a; border-radius: 8px; padding: 16px; margin-bottom: 24px; line-height: 1.6; font-size: 0.9rem; }
        .info-box code { background: #0d0d1a; padding: 2px 6px; border-radius: 3px; color: #ff8a80; }
        form { background: #1a1a2e; border: 1px solid #2a2a4a; border-radius: 8px; padding: 24px; margin-bottom: 24px; }
        label { display: block; margin-bottom: 6px; font-size: 0.9rem; color: #aaa; }
        input[type="text"], input[type="password"] {
            width: 100%; padding: 10px 12px; margin-bottom: 16px; border: 1px solid #333;
            border-radius: 6px; background: #0d0d1a; color: #e0e0e0; font-size: 0.95rem;
        }
        button { padding: 10px 24px; border: none; border-radius: 6px; background: #c62828; color: #fff; font-weight: bold; font-size: 0.95rem; cursor: pointer; }
        button:hover { background: #e53935; }
        .query-box { background: #0d0d1a; border: 1px solid #333; border-radius: 6px; padding: 14px; margin-bottom: 20px; font-family: 'Courier New', monospace; font-size: 0.85rem; color: #ffab91; word-break: break-all; }
        .query-box span { color: #666; }
        .result-box { background: #1a1a2e; border: 1px solid #2a2a4a; border-radius: 8px; padding: 16px; }
        .result-box h3 { color: #00e5ff; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 8px 12px; border-bottom: 1px solid #2a2a4a; font-size: 0.88rem; }
        th { color: #888; }
        .error { color: #ff5252; background: #1a1a2e; padding: 12px; border-radius: 6px; }
        .success { color: #69f0ae; }
        .fail { color: #ff8a80; }
    </style>
</head>
<body>
<div class="container">
    <a class="back" href="index.php">&larr; Back to Menu</a>
    <h1>Vulnerable Login</h1>
    <span class="tag">Vulnerable</span>

    <!-- Explanation -->
    <div class="info-box">
        <strong>How this works:</strong><br>
        The SQL query uses <strong>string concatenation</strong> to embed user input directly.<br><br>
        <strong>Try this payload in the username field:</strong><br>
        <code>admin' -- </code> &nbsp;(password can be anything)<br><br>
        <strong>What happens:</strong><br>
        The query becomes:<br>
        <code>SELECT * FROM users WHERE username='admin' -- ' AND password='...'</code><br>
        The <code>--</code> comments out the password check, granting access as admin.
    </div>

    <!-- Login Form -->
    <form method="POST">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="e.g. admin' -- " value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">

        <label for="password">Password</label>
        <input type="text" id="password" name="password" placeholder="anything" value="<?= htmlspecialchars($_POST['password'] ?? '') ?>">

        <button type="submit">Log In</button>
    </form>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <!-- Show the executed query -->
        <div class="query-box">
            <span>Executed SQL:</span><br>
            <?= htmlspecialchars($query) ?>
        </div>

        <!-- Show result -->
        <div class="result-box">
            <?php if ($error): ?>
                <p class="error"><strong>SQL Error:</strong> <?= htmlspecialchars($error) ?></p>

            <?php elseif ($result && count($result) > 0): ?>
                <h3 class="success">Login Successful &mdash; <?= count($result) ?> row(s) returned</h3>
                <table>
                    <tr><th>ID</th><th>Username</th><th>Password</th><th>Role</th></tr>
                    <?php foreach ($result as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['password']) ?></td>
                            <td><?= htmlspecialchars($row['role']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>

            <?php else: ?>
                <h3 class="fail">Login Failed &mdash; 0 rows returned</h3>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
