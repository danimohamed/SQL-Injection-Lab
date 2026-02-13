<?php
// ============================================================
// lang_switcher.php - Language Switcher Logic
// Include this file at the TOP of every page (before any HTML)
// ============================================================

session_start();

// Handle language switch via GET parameter
if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'fr'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

// Default language
$currentLang = $_SESSION['lang'] ?? 'en';

// Load translations
$lang = require __DIR__ . '/lang/' . $currentLang . '.php';

/**
 * Translation helper function
 * Usage: <?= t('key') ?>
 */
function t(string $key): string {
    global $lang;
    return $lang[$key] ?? $key;
}

/**
 * Renders the language switcher button HTML.
 * Call this inside the page body where you want the switcher to appear.
 */
function langSwitcherHTML(): string {
    global $currentLang;
    $targetLang = ($currentLang === 'en') ? 'fr' : 'en';
    $label = ($currentLang === 'en') ? '🇫🇷 Français' : '🇬🇧 English';

    // Preserve current URL and add/replace lang parameter
    $url = strtok($_SERVER['REQUEST_URI'], '?');
    $params = $_GET;
    $params['lang'] = $targetLang;
    $queryString = http_build_query($params);

    return '<a href="' . htmlspecialchars($url . '?' . $queryString) . '" class="lang-switch-btn">' . $label . '</a>';
}

/**
 * CSS for the language switcher button (add to <style> or include)
 */
function langSwitcherCSS(): string {
    return '
    .lang-switch-btn {
        position: fixed;
        top: 16px;
        right: 16px;
        background: #1a1a2e;
        color: #00e5ff;
        border: 1px solid #00e5ff;
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: bold;
        z-index: 1000;
        transition: background 0.2s, color 0.2s;
    }
    .lang-switch-btn:hover {
        background: #00e5ff;
        color: #0f0f1a;
    }
    ';
}
