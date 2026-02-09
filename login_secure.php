<?php
// ============================================================
// login_secure.php - Secure Login with Prepared Statements
// SQL Injection Educational Lab
// FOR EDUCATIONAL PURPOSES ONLY - localhost only
// ============================================================
//
// MITIGATION: PDO Prepared Statements with Bound Parameters
// ----------------------------------------------------------
// Instead of concatenating user input into the SQL string, we
// use placeholders (?) and pass values separately via execute().
//
// Why this is secure:
// 1. The SQL structure is sent to MySQL FIRST (the "prepare" step).
//    MySQL parses and compiles the query plan at this point.
// 2. The parameter values are sent SECOND (the "execute" step).
//    MySQL treats them purely as DATA, never as SQL code.
// 3. Even if the user types  admin' --  the entire string
//    (including the quote and dashes) is treated as a literal
//    username value. The query structure cannot be altered.
//
// This separation of CODE and DATA is the fundamental fix for
// SQL injection.
// ============================================================

require_once 'db.php';

$result  = null;
$error   = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        $pdo = getSecureDB();

        // =====================================================
        // SECURE: Prepared statement with bound parameters.
        // The ? placeholders can NEVER alter the query structure.
        // =====================================================
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
        $stmt->execute([$username, $password]);
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
    <title>Secure Login - SQL Injection Lab</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #0f0f1a; color: #e0e0e0; min-height: 100vh; }
        .container { max-width: 700px; margin: 0 auto; padding: 40px 20px; }
        a.back { color: #00e5ff; text-decoration: none; font-size: 0.9rem; }
        a.back:hover { text-decoration: underline; }
        h1 { color: #00c853; margin: 16px 0 4px; }
        .tag { display: inline-block; background: #1b5e20; color: #fff; font-size: 0.7rem; padding: 2px 8px; border-radius: 4px; text-transform: uppercase; font-weight: bold; margin-bottom: 16px; }
        .info-box { background: #1a1a2e; border: 1px solid #2a2a4a; border-radius: 8px; padding: 16px; margin-bottom: 24px; line-height: 1.6; font-size: 0.9rem; }
        .info-box code { background: #0d0d1a; padding: 2px 6px; border-radius: 3px; color: #69f0ae; }
        form { background: #1a1a2e; border: 1px solid #2a2a4a; border-radius: 8px; padding: 24px; margin-bottom: 24px; }
        label { display: block; margin-bottom: 6px; font-size: 0.9rem; color: #aaa; }
        input[type="text"], input[type="password"] {
            width: 100%; padding: 10px 12px; margin-bottom: 16px; border: 1px solid #333;
            border-radius: 6px; background: #0d0d1a; color: #e0e0e0; font-size: 0.95rem;
        }
        button { padding: 10px 24px; border: none; border-radius: 6px; background: #2e7d32; color: #fff; font-weight: bold; font-size: 0.95rem; cursor: pointer; }
        button:hover { background: #43a047; }
        .code-box { background: #0d0d1a; border: 1px solid #333; border-radius: 6px; padding: 14px; margin-bottom: 20px; font-family: 'Courier New', monospace; font-size: 0.85rem; color: #69f0ae; white-space: pre-wrap; }
        .result-box { background: #1a1a2e; border: 1px solid #2a2a4a; border-radius: 8px; padding: 16px; }
        .result-box h3 { margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 8px 12px; border-bottom: 1px solid #2a2a4a; font-size: 0.88rem; }
        th { color: #888; }
        .error { color: #ff5252; }
        .success { color: #69f0ae; }
        .fail { color: #ff8a80; }
        .comparison { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px; }
        .comparison > div { background: #0d0d1a; border-radius: 6px; padding: 14px; font-size: 0.85rem; line-height: 1.6; }
        .comparison h4 { margin-bottom: 8px; font-size: 0.9rem; }
        .vuln-code { border: 1px solid #c62828; }
        .vuln-code h4 { color: #ff5252; }
        .safe-code { border: 1px solid #2e7d32; }
        .safe-code h4 { color: #69f0ae; }
    </style>
</head>
<body>
<div class="container">
    <a class="back" href="index.php">&larr; Back to Menu</a>
    <h1>Secure Login</h1>
    <span class="tag">Secure</span>

    <!-- Explanation -->
    <div class="info-box">
        <strong>How prepared statements prevent SQL injection:</strong><br><br>
        1. The SQL query template is sent to MySQL with <code>?</code> placeholders.<br>
        2. MySQL compiles the query <em>before</em> seeing any user data.<br>
        3. User values are sent separately and treated as <strong>literal data</strong>, never as SQL code.<br><br>
        <strong>Try the same payloads from the vulnerable page:</strong><br>
        <code>admin' -- </code> &rarr; will fail (treated as a literal username string).<br>
        <code>' OR '1'='1</code> &rarr; will fail (no matching user with that exact name).
    </div>

    <!-- Side-by-side code comparison -->
    <div class="comparison">
        <div class="vuln-code">
            <h4>Vulnerable (string concat)</h4>
            <code style="color:#ff8a80;">$sql = "SELECT * FROM users<br>  WHERE username='$username'<br>  AND password='$password'";<br>$pdo->query($sql);</code>
        </div>
        <div class="safe-code">
            <h4>Secure (prepared statement)</h4>
            <code style="color:#69f0ae;">$stmt = $pdo->prepare(<br>  "SELECT * FROM users<br>  WHERE username = ?<br>  AND password = ?");<br>$stmt->execute([$user, $pass]);</code>
        </div>
    </div>

    <!-- Login Form -->
    <form method="POST">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Try: admin' -- " value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">

        <label for="password">Password</label>
        <input type="text" id="password" name="password" placeholder="Try: ' OR '1'='1" value="<?= htmlspecialchars($_POST['password'] ?? '') ?>">

        <button type="submit">Log In (Secure)</button>
    </form>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <!-- Show what the prepared statement looks like -->
        <div class="code-box">Prepared SQL:  SELECT * FROM users WHERE username = ? AND password = ?
Bound params: [<?= htmlspecialchars(json_encode($username)) ?>, <?= htmlspecialchars(json_encode($password)) ?>]</div>

        <div class="result-box">
            <?php if ($error): ?>
                <p class="error"><strong>Error:</strong> <?= htmlspecialchars($error) ?></p>

            <?php elseif ($result && count($result) > 0): ?>
                <h3 class="success">Login Successful &mdash; Legitimate credentials</h3>
                <table>
                    <tr><th>ID</th><th>Username</th><th>Role</th></tr>
                    <?php foreach ($result as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['role']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>

            <?php else: ?>
                <h3 class="fail">Login Failed &mdash; Injection attempt blocked</h3>
                <p style="color:#aaa; margin-top:8px; font-size:0.88rem;">
                    The payload was treated as a literal string value.<br>
                    No user named <code style="color:#ff8a80;"><?= htmlspecialchars($username) ?></code> exists in the database.
                </p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
