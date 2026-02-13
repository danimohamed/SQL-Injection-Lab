<?php
// ============================================================
// quiz.php - SQL Injection Knowledge Quiz
// SQL Injection Educational Lab
// FOR EDUCATIONAL PURPOSES ONLY - localhost only
// ============================================================

require_once 'lang_switcher.php';

// Quiz questions are defined in the language files under quiz_* keys
$questions = [
    ['key' => 'q1', 'correct' => 'b'],
    ['key' => 'q2', 'correct' => 'c'],
    ['key' => 'q3', 'correct' => 'a'],
    ['key' => 'q4', 'correct' => 'b'],
    ['key' => 'q5', 'correct' => 'c'],
    ['key' => 'q6', 'correct' => 'a'],
    ['key' => 'q7', 'correct' => 'b'],
    ['key' => 'q8', 'correct' => 'd'],
    ['key' => 'q9', 'correct' => 'a'],
    ['key' => 'q10', 'correct' => 'c'],
];

$submitted = false;
$score = 0;
$total = count($questions);
$answers = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted = true;
    foreach ($questions as $i => $q) {
        $userAnswer = $_POST['q' . ($i + 1)] ?? '';
        $answers[$i] = $userAnswer;
        if ($userAnswer === $q['correct']) {
            $score++;
        }
    }
    // Store score in session for progress tracking
    $_SESSION['quiz_score'] = $score;
    $_SESSION['quiz_total'] = $total;
    $_SESSION['quiz_completed'] = true;
}
?>
<!DOCTYPE html>
<html lang="<?= t('lang_code') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('quiz_page') ?></title>
    <style>
        <?= langSwitcherCSS() ?>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: #0f0f1a; color: #e0e0e0; min-height: 100vh; }
        .container { max-width: 800px; margin: 0 auto; padding: 40px 20px; }
        a.back { color: #00e5ff; text-decoration: none; font-size: 0.9rem; }
        a.back:hover { text-decoration: underline; }
        h1 { color: #00e5ff; margin: 16px 0 8px; }
        .tag { display: inline-block; background: #0d47a1; color: #fff; font-size: 0.7rem; padding: 2px 8px; border-radius: 4px; text-transform: uppercase; font-weight: bold; margin-bottom: 24px; }

        .score-banner { text-align: center; padding: 24px; border-radius: 10px; margin-bottom: 24px; font-size: 1.2rem; }
        .score-banner.excellent { background: #1b5e20; color: #69f0ae; border: 2px solid #2e7d32; }
        .score-banner.good { background: #e6510033; color: #ffab40; border: 2px solid #e65100; }
        .score-banner.poor { background: #b71c1c33; color: #ff5252; border: 2px solid #c62828; }
        .score-banner .big { font-size: 2.5rem; font-weight: bold; display: block; margin-bottom: 4px; }
        .score-banner .label { font-size: 0.9rem; opacity: 0.8; }
        .progress-bar { background: #0d0d1a; border-radius: 20px; height: 12px; margin: 16px auto; max-width: 300px; overflow: hidden; }
        .progress-bar .fill { height: 100%; border-radius: 20px; transition: width 0.5s; }

        .question-card { background: #1a1a2e; border: 1px solid #2a2a4a; border-radius: 8px; padding: 20px; margin-bottom: 16px; transition: border-color 0.2s; }
        .question-card.correct { border-color: #2e7d32; }
        .question-card.wrong { border-color: #c62828; }
        .question-card .q-number { color: #00e5ff; font-size: 0.8rem; font-weight: bold; text-transform: uppercase; margin-bottom: 8px; }
        .question-card .q-text { font-size: 0.95rem; margin-bottom: 14px; line-height: 1.5; }
        .question-card .q-text code { background: #0d0d1a; padding: 2px 6px; border-radius: 3px; color: #ff8a80; font-size: 0.88rem; }

        .options { list-style: none; }
        .options li { margin-bottom: 8px; }
        .options label { display: flex; align-items: center; gap: 10px; padding: 8px 12px; border-radius: 6px; cursor: pointer; font-size: 0.9rem; border: 1px solid transparent; transition: all 0.15s; }
        .options label:hover { background: #0d0d1a; }
        .options input[type="radio"] { accent-color: #00e5ff; width: 16px; height: 16px; }
        .options label.selected { background: #0d0d1a; border-color: #00e5ff; }
        .options label.correct-answer { background: #1b5e2055; border-color: #2e7d32; }
        .options label.wrong-answer { background: #b71c1c33; border-color: #c62828; text-decoration: line-through; opacity: 0.7; }
        .result-icon { font-size: 1.1rem; margin-left: auto; }

        .explanation { background: #0d0d1a; border-radius: 6px; padding: 10px 14px; margin-top: 10px; font-size: 0.82rem; color: #aaa; line-height: 1.5; border-left: 3px solid #00e5ff; }

        .submit-area { text-align: center; margin: 32px 0; }
        button[type="submit"] { padding: 14px 48px; border: none; border-radius: 8px; background: #0d47a1; color: #fff; font-weight: bold; font-size: 1.1rem; cursor: pointer; transition: background 0.2s; }
        button[type="submit"]:hover { background: #1565c0; }
        .retry-btn { display: inline-block; padding: 12px 36px; border-radius: 8px; background: #37474f; color: #fff; font-weight: bold; font-size: 1rem; text-decoration: none; margin-top: 16px; transition: background 0.2s; }
        .retry-btn:hover { background: #546e7a; }
    </style>
</head>
<body>
<?= langSwitcherHTML() ?>
<div class="container">
    <a class="back" href="index.php"><?= t('back_to_menu') ?></a>
    <h1><?= t('quiz_title') ?></h1>
    <span class="tag"><?= t('quiz_tag') ?></span>

    <?php if ($submitted): ?>
        <!-- Score Banner -->
        <?php
        $pct = round(($score / $total) * 100);
        $grade = ($pct >= 80) ? 'excellent' : (($pct >= 50) ? 'good' : 'poor');
        ?>
        <div class="score-banner <?= $grade ?>">
            <span class="big"><?= $score ?> / <?= $total ?></span>
            <span class="label"><?= $pct ?>% &mdash; <?= t('quiz_grade_' . $grade) ?></span>
            <div class="progress-bar">
                <div class="fill" style="width: <?= $pct ?>%; background: <?= $grade === 'excellent' ? '#69f0ae' : ($grade === 'good' ? '#ffab40' : '#ff5252') ?>;"></div>
            </div>
        </div>
    <?php endif; ?>

    <form method="POST">
        <?php foreach ($questions as $i => $q): ?>
            <?php
            $qNum = $i + 1;
            $cardClass = '';
            if ($submitted) {
                $cardClass = ($answers[$i] === $q['correct']) ? 'correct' : 'wrong';
            }
            ?>
            <div class="question-card <?= $cardClass ?>">
                <div class="q-number"><?= t('quiz_question') ?> <?= $qNum ?>/<?= $total ?></div>
                <div class="q-text"><?= t('quiz_' . $q['key']) ?></div>
                <ul class="options">
                    <?php foreach (['a', 'b', 'c', 'd'] as $opt): ?>
                        <?php
                        $optKey = 'quiz_' . $q['key'] . '_' . $opt;
                        $optText = t($optKey);
                        if ($optText === $optKey) continue; // skip if no translation (some questions have 3 options)

                        $labelClass = '';
                        $icon = '';
                        if ($submitted) {
                            if ($opt === $q['correct']) {
                                $labelClass = 'correct-answer';
                                $icon = '&#10003;';
                            } elseif ($opt === ($answers[$i] ?? '') && $opt !== $q['correct']) {
                                $labelClass = 'wrong-answer';
                                $icon = '&#10007;';
                            }
                        } elseif (isset($answers[$i]) && $answers[$i] === $opt) {
                            $labelClass = 'selected';
                        }
                        ?>
                        <li>
                            <label class="<?= $labelClass ?>">
                                <input type="radio" name="q<?= $qNum ?>" value="<?= $opt ?>"
                                    <?= (isset($answers[$i]) && $answers[$i] === $opt) ? 'checked' : '' ?>
                                    <?= $submitted ? 'disabled' : '' ?>>
                                <?= $optText ?>
                                <?php if ($icon): ?><span class="result-icon"><?= $icon ?></span><?php endif; ?>
                            </label>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php if ($submitted): ?>
                    <div class="explanation"><?= t('quiz_' . $q['key'] . '_explain') ?></div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <div class="submit-area">
            <?php if (!$submitted): ?>
                <button type="submit"><?= t('quiz_submit') ?></button>
            <?php else: ?>
                <a href="quiz.php" class="retry-btn"><?= t('quiz_retry') ?></a>
            <?php endif; ?>
        </div>
    </form>
</div>
</body>
</html>
