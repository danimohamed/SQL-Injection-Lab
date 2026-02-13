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
require_once 'lang_switcher.php';
$_SESSION['visited_labs'][] = 'login_secure'; $_SESSION['visited_labs'] = array_unique($_SESSION['visited_labs']);

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
    <title><?= t('secure_login_page') ?></title>
    <style>
        <?= langSwitcherCSS() ?>
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
<?= langSwitcherHTML() ?>
<div class="container">
    <a class="back" href="index.php"><?= t('back_to_menu') ?></a>
    <h1><?= t('secure_login_title') ?></h1>
    <span class="tag"><?= t('secure') ?></span>

    <!-- Explanation -->
    <div class="info-box">
        <?= t('secure_login_howto') ?>
    </div>

    <!-- Side-by-side code comparison -->
    <div class="comparison">
        <div class="vuln-code">
            <h4><?= t('vuln_code_title') ?></h4>
            <code style="color:#ff8a80;">$sql = "SELECT * FROM users<br>  WHERE username='$username'<br>  AND password='$password'";<br>$pdo->query($sql);</code>
        </div>
        <div class="safe-code">
            <h4><?= t('safe_code_title') ?></h4>
            <code style="color:#69f0ae;">$stmt = $pdo->prepare(<br>  "SELECT * FROM users<br>  WHERE username = ?<br>  AND password = ?");<br>$stmt->execute([$user, $pass]);</code>
        </div>
    </div>

    <!-- Login Form -->
    <form method="POST">
        <label for="username"><?= t('username') ?></label>
        <input type="text" id="username" name="username" placeholder="Try: admin' -- " value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">

        <label for="password"><?= t('password') ?></label>
        <input type="text" id="password" name="password" placeholder="Try: ' OR '1'='1" value="<?= htmlspecialchars($_POST['password'] ?? '') ?>">

        <button type="submit"><?= t('login_secure_btn') ?></button>
    </form>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <!-- Show what the prepared statement looks like -->
        <div class="code-box"><?= t('prepared_sql') ?>  SELECT * FROM users WHERE username = ? AND password = ?
<?= t('bound_params') ?> [<?= htmlspecialchars(json_encode($username)) ?>, <?= htmlspecialchars(json_encode($password)) ?>]</div>

        <div class="result-box">
            <?php if ($error): ?>
                <p class="error"><strong><?= t('error') ?></strong> <?= htmlspecialchars($error) ?></p>

            <?php elseif ($result && count($result) > 0): ?>
                <h3 class="success"><?= t('login_legit') ?></h3>
                <table>
                    <tr><th><?= t('id') ?></th><th><?= t('username') ?></th><th><?= t('role') ?></th></tr>
                    <?php foreach ($result as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['role']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>

            <?php else: ?>
                <h3 class="fail"><?= t('login_blocked') ?></h3>
                <p style="color:#aaa; margin-top:8px; font-size:0.88rem;">
                    <?= t('payload_literal') ?><br>
                    <?= t('no_user_named') ?> <code style="color:#ff8a80;"><?= htmlspecialchars($username) ?></code> <?= t('exists_in_db') ?>
                </p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
