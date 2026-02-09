<?php
// ============================================================
// read_vulnerable.php - Data Extraction Demo (OR 1=1 + UNION)
// SQL Injection Educational Lab
// FOR EDUCATIONAL PURPOSES ONLY - localhost only
// ============================================================
//
// VULNERABILITY 1: OR-based injection (dump all rows)
// ---------------------------------------------------
// The search query uses string concatenation. By injecting
//   ' OR '1'='1
// the WHERE clause always evaluates to TRUE, returning every row.
//
// Original query:
//   SELECT * FROM users WHERE username='<input>'
// Injected query:
//   SELECT * FROM users WHERE username='' OR '1'='1'
//
//
// VULNERABILITY 2: UNION-based injection (extract specific data)
// --------------------------------------------------------------
// UNION SELECT lets an attacker append a second query to extract
// data from any column or even other tables.
//
// Payload:
//   ' UNION SELECT id, username, password, role FROM users --
//
// The attacker can also probe the MySQL metadata:
//   ' UNION SELECT 1, table_name, column_name, 4 FROM information_schema.columns WHERE table_schema='sql_injection_lab' --
// ============================================================

require_once 'db.php';

$result  = null;
$error   = null;
$query   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search = $_POST['search'] ?? '';

    // =====================================================
    // VULNERABLE: Direct string concatenation
    // =====================================================
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Extraction - SQL Injection Lab</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #0f0f1a; color: #e0e0e0; min-height: 100vh; }
        .container { max-width: 750px; margin: 0 auto; padding: 40px 20px; }
        a.back { color: #00e5ff; text-decoration: none; font-size: 0.9rem; }
        a.back:hover { text-decoration: underline; }
        h1 { color: #ff5252; margin: 16px 0 4px; }
        .tag { display: inline-block; background: #b71c1c; color: #fff; font-size: 0.7rem; padding: 2px 8px; border-radius: 4px; text-transform: uppercase; font-weight: bold; margin-bottom: 16px; }
        .info-box { background: #1a1a2e; border: 1px solid #2a2a4a; border-radius: 8px; padding: 16px; margin-bottom: 24px; line-height: 1.6; font-size: 0.9rem; }
        .info-box code { display: block; background: #0d0d1a; padding: 6px 10px; border-radius: 4px; color: #ff8a80; margin: 6px 0; font-size: 0.85rem; }
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
        .error { color: #ff5252; background: #1a1a2e; padding: 12px; border-radius: 6px; }
    </style>
</head>
<body>
<div class="container">
    <a class="back" href="index.php">&larr; Back to Menu</a>
    <h1>Data Extraction</h1>
    <span class="tag">Vulnerable</span>

    <!-- Explanation -->
    <div class="info-box">
        <strong>Lab A &mdash; Dump all rows (OR 1=1):</strong><br>
        Enter the following to retrieve every user:
        <code>' OR '1'='1</code>

        <strong>Lab B &mdash; UNION injection (extract specific data):</strong><br>
        Use UNION SELECT to control which columns appear:
        <code>' UNION SELECT id, username, password, role FROM users -- </code>

        <strong>Lab C &mdash; Explore the database schema:</strong><br>
        Extract table and column names from MySQL metadata:
        <code>' UNION SELECT 1, table_name, column_name, table_schema FROM information_schema.columns WHERE table_schema='sql_injection_lab' -- </code>
    </div>

    <!-- Search Form -->
    <form method="POST">
        <label for="search">Search by Username</label>
        <input type="text" id="search" name="search" placeholder="e.g. admin  or  ' OR '1'='1" value="<?= htmlspecialchars($_POST['search'] ?? '') ?>">
        <button type="submit">Search</button>
    </form>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <!-- Show the executed query -->
        <div class="query-box">
            <span>Executed SQL:</span><br>
            <?= htmlspecialchars($query) ?>
        </div>

        <div class="result-box">
            <?php if ($error): ?>
                <p class="error"><strong>SQL Error:</strong> <?= htmlspecialchars($error) ?></p>

            <?php elseif ($result && count($result) > 0): ?>
                <h3><?= count($result) ?> row(s) returned</h3>
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
                <h3 style="color:#ff8a80;">No results found.</h3>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
