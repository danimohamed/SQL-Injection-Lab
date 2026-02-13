<?php
// ============================================================
// delete_vulnerable.php - DELETE Injection Demo
// SQL Injection Educational Lab
// FOR EDUCATIONAL PURPOSES ONLY - localhost only
// ============================================================
//
// VULNERABILITY: DELETE injection via string concatenation
// --------------------------------------------------------
// The form lets a user "delete their account". The query is:
//   DELETE FROM users WHERE username='<input>'
//
// By injecting   ' OR '1'='1   the WHERE clause matches every
// row, wiping the entire table.
//
// Payload:
//   ' OR '1'='1
//
// Resulting query:
//   DELETE FROM users WHERE username='' OR '1'='1'
//
// Since '1'='1' is always true, ALL rows are deleted.
// ============================================================

require_once 'db.php';
require_once 'lang_switcher.php';
$_SESSION['visited_labs'][] = 'delete_vulnerable'; $_SESSION['visited_labs'] = array_unique($_SESSION['visited_labs']);

$message = null;
$error   = null;
$query   = '';
$users   = [];

// Handle reset
if (isset($_POST['reset'])) {
    resetUsersTable();
    $message = t('db_reset_msg');
}

// Handle delete
if (isset($_POST['delete'])) {
    $username = $_POST['username'] ?? '';

    // =====================================================
    // VULNERABLE: Direct string concatenation in DELETE
    // =====================================================
    $query = "DELETE FROM users WHERE username='$username'";

    try {
        $pdo   = getDB();
        $count = $pdo->exec($query);
        $message = t('delete_executed') . " $count " . t('rows_affected');
    } catch (PDOException $e) {
        $error = $e->getMessage();
    }
}

// Always show current table state
try {
    $pdo   = getDB();
    $users = $pdo->query("SELECT * FROM users ORDER BY id")->fetchAll();
} catch (PDOException $e) {
    // Table might be empty, that's expected after injection
    $users = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('delete_page') ?></title>
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
        form { background: #1a1a2e; border: 1px solid #2a2a4a; border-radius: 8px; padding: 24px; margin-bottom: 24px; }
        label { display: block; margin-bottom: 6px; font-size: 0.9rem; color: #aaa; }
        input[type="text"] {
            width: 100%; padding: 10px 12px; margin-bottom: 16px; border: 1px solid #333;
            border-radius: 6px; background: #0d0d1a; color: #e0e0e0; font-size: 0.95rem;
        }
        .btn-row { display: flex; gap: 12px; }
        button { padding: 10px 24px; border: none; border-radius: 6px; font-weight: bold; font-size: 0.95rem; cursor: pointer; }
        button[name="delete"] { background: #c62828; color: #fff; }
        button[name="delete"]:hover { background: #e53935; }
        button[name="reset"] { background: #37474f; color: #fff; }
        button[name="reset"]:hover { background: #546e7a; }
        .query-box { background: #0d0d1a; border: 1px solid #333; border-radius: 6px; padding: 14px; margin-bottom: 20px; font-family: 'Courier New', monospace; font-size: 0.85rem; color: #ffab91; word-break: break-all; }
        .query-box span { color: #666; }
        .result-box { background: #1a1a2e; border: 1px solid #2a2a4a; border-radius: 8px; padding: 16px; margin-bottom: 16px; }
        .result-box h3 { color: #00e5ff; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 8px 12px; border-bottom: 1px solid #2a2a4a; font-size: 0.88rem; }
        th { color: #888; }
        .msg { padding: 10px 14px; border-radius: 6px; margin-bottom: 16px; font-size: 0.9rem; }
        .msg.ok { background: #1b5e20; color: #69f0ae; }
        .msg.err { background: #b71c1c33; color: #ff5252; }
        .empty { color: #ff8a80; text-align: center; padding: 20px; font-style: italic; }
    </style>
</head>
<body>
<?= langSwitcherHTML() ?>
<div class="container">
    <a class="back" href="index.php"><?= t('back_to_menu') ?></a>
    <h1><?= t('delete_title') ?></h1>
    <span class="tag"><?= t('vulnerable') ?></span>

    <!-- Explanation -->
    <div class="info-box">
        <?= t('delete_scenario') ?><br><br>
        <?= t('delete_normal') ?><br><br>
        <?= t('delete_malicious') ?>
        <code>' OR '1'='1</code>
        <?= t('delete_result') ?>
        <code>DELETE FROM users WHERE username='' OR '1'='1'</code>
        <?= t('delete_all_rows') ?><br><br>
        <?= t('delete_restore') ?>
    </div>

    <!-- Form -->
    <form method="POST">
        <label for="username"><?= t('username_to_delete') ?></label>
        <input type="text" id="username" name="username" placeholder="e.g. user2  or  ' OR '1'='1" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">

        <div class="btn-row">
            <button type="submit" name="delete" value="1"><?= t('delete_user_btn') ?></button>
            <button type="submit" name="reset" value="1"><?= t('reset_database') ?></button>
        </div>
    </form>

    <?php if ($message): ?>
        <div class="msg ok"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="msg err"><strong><?= t('sql_error') ?></strong> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($query): ?>
        <div class="query-box">
            <span><?= t('executed_sql') ?></span><br>
            <?= htmlspecialchars($query) ?>
        </div>
    <?php endif; ?>

    <!-- Current Table State -->
    <div class="result-box">
        <h3><?= t('current_users') ?></h3>
        <?php if (count($users) > 0): ?>
            <table>
                <tr><th><?= t('id') ?></th><th><?= t('username') ?></th><th><?= t('password') ?></th><th><?= t('role') ?></th></tr>
                <?php foreach ($users as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['password']) ?></td>
                        <td><?= htmlspecialchars($row['role']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p class="empty"><?= t('table_empty') ?></p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
