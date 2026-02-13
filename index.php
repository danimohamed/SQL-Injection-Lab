<?php
// ============================================================
// index.php - Main Menu / Landing Page
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
    <title><?= t('site_title') ?></title>
    <style>
        <?= langSwitcherCSS() ?>
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
        .tag.ref    { background: #0d47a1; color: #fff; }
        .tag.quiz   { background: #4a148c; color: #fff; }

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
        .btn.blue  { background: #0d47a1; color: #fff; }
        .btn.blue:hover { background: #1565c0; }
        .btn.purple { background: #4a148c; color: #fff; }
        .btn.purple:hover { background: #6a1b9a; }

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
        .social-links {
            margin-top: 10px;
            display: flex;
            justify-content: center;
            gap: 16px;
        }
        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #1a1a2e;
            border: 1px solid #2a2a4a;
            transition: all 0.3s;
        }
        .social-links a:hover {
            transform: translateY(-3px);
            border-color: #00e5ff;
        }
        .social-links a svg {
            width: 18px;
            height: 18px;
            fill: #888;
            transition: fill 0.3s;
        }
        .social-links a:hover svg { fill: #00e5ff; }

        /* ---- Progress Tracker ---- */
        .progress-section {
            background: #1a1a2e;
            border: 1px solid #2a2a4a;
            border-radius: 10px;
            padding: 20px 24px;
            margin-bottom: 36px;
        }
        .progress-section h2 {
            color: #00e5ff;
            font-size: 1.1rem;
            margin-bottom: 14px;
        }
        .progress-bar-outer {
            background: #0d0d1a;
            border-radius: 20px;
            height: 16px;
            overflow: hidden;
            margin-bottom: 12px;
        }
        .progress-bar-fill {
            height: 100%;
            border-radius: 20px;
            background: linear-gradient(90deg, #00e5ff, #69f0ae);
            transition: width 0.5s;
        }
        .progress-stats {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            color: #aaa;
        }
        .progress-stats strong { color: #00e5ff; }
        .lab-badges { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 10px; }
        .lab-badge {
            font-size: 0.72rem;
            padding: 3px 10px;
            border-radius: 12px;
            font-weight: bold;
        }
        .lab-badge.done { background: #1b5e20; color: #69f0ae; }
        .lab-badge.pending { background: #0d0d1a; color: #555; border: 1px solid #333; }

        /* ---- Section Dividers ---- */
        .section-label {
            color: #666;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: bold;
            margin: 28px 0 12px;
            padding-bottom: 6px;
            border-bottom: 1px solid #2a2a4a;
        }
    </style>
</head>
<body>
    <?= langSwitcherHTML() ?>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <img class="logo" src="https://student.emsi.ma/app_logo_emsi.png" alt="EMSI Logo">
            <h1><?= t('main_title') ?></h1>
        </div>

        <!-- Course Info Banner -->
        <div class="warning-banner">
            <?= t('course_banner') ?>
        </div>

        <!-- Progress Tracker -->
        <?php
        // Track lab visits
        $allLabs = ['login_vulnerable', 'read_vulnerable', 'update_vulnerable', 'delete_vulnerable', 'blind_vulnerable', 'login_secure', 'timebased_vulnerable', 'error_based_vulnerable'];
        $labNames = [
            'login_vulnerable' => t('card1_title'),
            'read_vulnerable' => t('card2_title'),
            'update_vulnerable' => t('card3_title'),
            'delete_vulnerable' => t('card4_title'),
            'blind_vulnerable' => t('card5_title'),
            'login_secure' => t('card6_title'),
            'timebased_vulnerable' => t('card7_title'),
            'error_based_vulnerable' => t('card8_title'),
        ];
        $visited = $_SESSION['visited_labs'] ?? [];
        $visitedCount = count($visited);
        $totalLabs = count($allLabs);
        $pctProgress = round(($visitedCount / $totalLabs) * 100);
        $quizDone = $_SESSION['quiz_completed'] ?? false;
        $quizScore = $_SESSION['quiz_score'] ?? 0;
        $quizTotal = $_SESSION['quiz_total'] ?? 10;
        
        // Handle progress reset
        if (isset($_GET['reset_progress'])) {
            unset($_SESSION['visited_labs'], $_SESSION['quiz_completed'], $_SESSION['quiz_score'], $_SESSION['quiz_total']);
            header('Location: index.php');
            exit;
        }
        ?>
        <div class="progress-section">
            <h2><?= t('progress_title') ?></h2>
            <div class="progress-bar-outer">
                <div class="progress-bar-fill" style="width: <?= $pctProgress ?>%;"></div>
            </div>
            <div class="progress-stats">
                <span><?= t('labs_visited') ?>: <strong><?= $visitedCount ?>/<?= $totalLabs ?></strong></span>
                <span><?= t('quiz_score_label') ?>: <strong><?= $quizDone ? "$quizScore/$quizTotal" : t('not_taken') ?></strong></span>
                <a href="?reset_progress=1" style="color:#ff5252; text-decoration:none; font-size:0.82rem;"><?= t('reset_progress') ?></a>
            </div>
            <div class="lab-badges">
                <?php foreach ($allLabs as $lab): ?>
                    <span class="lab-badge <?= in_array($lab, $visited) ? 'done' : 'pending' ?>">
                        <?= $labNames[$lab] ?? $lab ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Lab Cards -->
        <div class="section-label">&#9888; <?= t('vulnerable') ?> Labs</div>
        <div class="grid">

            <!-- 1. Vulnerable Login -->
            <div class="card">
                <h2><?= t('card1_title') ?></h2>
                <span class="tag vuln"><?= t('vulnerable') ?></span>
                <p><?= t('card1_desc') ?></p>
                <code>admin' --</code>
                <a class="btn red" href="login_vulnerable.php"><?= t('open_lab') ?></a>
            </div>

            <!-- 2. Data Extraction (OR 1=1) + UNION -->
            <div class="card">
                <h2><?= t('card2_title') ?></h2>
                <span class="tag vuln"><?= t('vulnerable') ?></span>
                <p><?= t('card2_desc') ?></p>
                <code>' OR '1'='1</code>
                <a class="btn red" href="read_vulnerable.php"><?= t('open_lab') ?></a>
            </div>

            <!-- 3. Update Injection -->
            <div class="card">
                <h2><?= t('card3_title') ?></h2>
                <span class="tag vuln"><?= t('vulnerable') ?></span>
                <p><?= t('card3_desc') ?></p>
                <code>user1', role='admin' WHERE username='user1' --</code>
                <a class="btn red" href="update_vulnerable.php"><?= t('open_lab') ?></a>
            </div>

            <!-- 4. Delete Injection -->
            <div class="card">
                <h2><?= t('card4_title') ?></h2>
                <span class="tag vuln"><?= t('vulnerable') ?></span>
                <p><?= t('card4_desc') ?></p>
                <code>' OR '1'='1</code>
                <a class="btn red" href="delete_vulnerable.php"><?= t('open_lab') ?></a>
            </div>

            <!-- 5. Blind SQL Injection -->
            <div class="card">
                <h2><?= t('card5_title') ?></h2>
                <span class="tag vuln"><?= t('vulnerable') ?></span>
                <p><?= t('card5_desc') ?></p>
                <code>admin' AND SUBSTRING(password,1,1)='a' --</code>
                <a class="btn red" href="blind_vulnerable.php"><?= t('open_lab') ?></a>
            </div>

            <!-- 6. Secure Login -->
            <div class="card secure">
                <h2><?= t('card6_title') ?></h2>
                <span class="tag safe"><?= t('secure') ?></span>
                <p><?= t('card6_desc') ?></p>
                <code>Prepared statements + bound parameters</code>
                <a class="btn green" href="login_secure.php"><?= t('open_lab') ?></a>
            </div>

            <!-- 7. Time-Based Blind SQLi -->
            <div class="card">
                <h2><?= t('card7_title') ?></h2>
                <span class="tag vuln"><?= t('vulnerable') ?></span>
                <p><?= t('card7_desc') ?></p>
                <code>admin' AND IF(SUBSTRING(password,1,1)='a', SLEEP(2), 0) --</code>
                <a class="btn red" href="timebased_vulnerable.php"><?= t('open_lab') ?></a>
            </div>

            <!-- 8. Error-Based SQLi -->
            <div class="card">
                <h2><?= t('card8_title') ?></h2>
                <span class="tag vuln"><?= t('vulnerable') ?></span>
                <p><?= t('card8_desc') ?></p>
                <code>' AND EXTRACTVALUE(1, CONCAT(0x7e, version())) --</code>
                <a class="btn red" href="error_based_vulnerable.php"><?= t('open_lab') ?></a>
            </div>

        </div><!-- /grid -->

        <!-- Tools Section -->
        <div class="section-label">&#128218; <?= t('reference') ?> & <?= t('assessment') ?></div>
        <div class="grid">

            <!-- 9. Cheat Sheet -->
            <div class="card" style="border-color: #0d47a1;">
                <h2><?= t('card9_title') ?></h2>
                <span class="tag ref"><?= t('reference') ?></span>
                <p><?= t('card9_desc') ?></p>
                <code>UNION, BLIND, ERROR, TIME, PREVENTION</code>
                <a class="btn blue" href="cheatsheet.php"><?= t('open_lab') ?></a>
            </div>

            <!-- 10. Quiz -->
            <div class="card" style="border-color: #4a148c;">
                <h2><?= t('card10_title') ?></h2>
                <span class="tag quiz"><?= t('assessment') ?></span>
                <p><?= t('card10_desc') ?></p>
                <code>10 <?= t('quiz_question') ?>s &bull; <?= t('assessment') ?></code>
                <a class="btn purple" href="quiz.php"><?= t('open_lab') ?></a>
            </div>

        </div><!-- /grid -->

        <!-- Footer -->
        <div class="footer">
            <?= t('footer_dev') ?> <strong>Mohamed Dani</strong>
            <div class="social-links">
                <a href="https://github.com/danimohamed" target="_blank" title="GitHub">
                    <svg viewBox="0 0 24 24"><path d="M12 .3a12 12 0 00-3.8 23.4c.6.1.8-.3.8-.6v-2c-3.3.7-4-1.6-4-1.6-.5-1.4-1.3-1.8-1.3-1.8-1-.7.1-.7.1-.7 1.2.1 1.8 1.2 1.8 1.2 1 1.8 2.8 1.3 3.5 1 .1-.8.4-1.3.7-1.6-2.7-.3-5.5-1.3-5.5-6 0-1.2.5-2.3 1.2-3.1-.1-.4-.5-1.5.1-3.2 0 0 1-.3 3.4 1.2a11.5 11.5 0 016 0c2.3-1.5 3.3-1.2 3.3-1.2.7 1.7.3 2.8.1 3.2.8.8 1.2 1.9 1.2 3.1 0 4.6-2.8 5.6-5.5 5.9.5.4.9 1.2.9 2.4v3.5c0 .3.2.7.8.6A12 12 0 0012 .3"/></svg>
                </a>
                <a href="https://www.instagram.com/mohameddani11/?hl=fr" target="_blank" title="Instagram">
                    <svg viewBox="0 0 24 24"><path d="M12 2.2c2.7 0 3 0 4.1.1 1 0 1.5.2 1.9.4.5.2.8.4 1.1.7.3.3.5.7.7 1.1.2.4.3 1 .4 1.9 0 1 .1 1.4.1 4.1s0 3-.1 4.1c0 1-.2 1.5-.4 1.9-.2.5-.4.8-.7 1.1-.3.3-.7.5-1.1.7-.4.2-1 .3-1.9.4-1 0-1.4.1-4.1.1s-3 0-4.1-.1c-1 0-1.5-.2-1.9-.4-.5-.2-.8-.4-1.1-.7-.3-.3-.5-.7-.7-1.1-.2-.4-.3-1-.4-1.9 0-1-.1-1.4-.1-4.1s0-3 .1-4.1c0-1 .2-1.5.4-1.9.2-.5.4-.8.7-1.1.3-.3.7-.5 1.1-.7.4-.2 1-.3 1.9-.4 1 0 1.4-.1 4.1-.1M12 0C9.3 0 8.9 0 7.9.1 6.9.1 6.1.3 5.5.6c-.7.3-1.3.6-1.9 1.2C3 2.4 2.6 3 2.3 3.6c-.3.7-.5 1.4-.5 2.4C1.7 7 1.7 7.4 1.7 12s0 5 .1 6c.1 1 .3 1.8.6 2.4.3.7.6 1.3 1.2 1.9.6.6 1.2.9 1.9 1.2.6.3 1.4.5 2.4.5 1 .1 1.4.1 4.1.1s3 0 4.1-.1c1-.1 1.8-.3 2.4-.5.7-.3 1.3-.6 1.9-1.2.6-.6.9-1.2 1.2-1.9.3-.6.5-1.4.5-2.4.1-1 .1-1.4.1-4.1s0-3-.1-4.1c-.1-1-.3-1.8-.5-2.4-.3-.7-.6-1.3-1.2-1.9-.6-.6-1.2-.9-1.9-1.2-.6-.3-1.4-.5-2.4-.5C15 0 14.6 0 12 0zm0 5.8a6.2 6.2 0 100 12.4 6.2 6.2 0 000-12.4zM12 16a4 4 0 110-8 4 4 0 010 8zm6.4-10.8a1.4 1.4 0 100 2.8 1.4 1.4 0 000-2.8z"/></svg>
                </a>
                <a href="https://www.linkedin.com/in/mohamed-dani/" target="_blank" title="LinkedIn">
                    <svg viewBox="0 0 24 24"><path d="M20.4 20.5h-3.6v-5.6c0-1.3 0-3-1.8-3s-2.1 1.4-2.1 2.9v5.7H9.4V9h3.4v1.6h.1c.5-.9 1.6-1.8 3.4-1.8 3.6 0 4.3 2.4 4.3 5.5v6.2zM5.3 7.4a2.1 2.1 0 110-4.2 2.1 2.1 0 010 4.2zM7.1 20.5H3.5V9h3.6v11.5zM22.2 0H1.8C.8 0 0 .8 0 1.8v20.4c0 1 .8 1.8 1.8 1.8h20.4c1 0 1.8-.8 1.8-1.8V1.8c0-1-.8-1.8-1.8-1.8z"/></svg>
                </a>
            </div>
        </div>

    </div><!-- /container -->
</body>
</html>
