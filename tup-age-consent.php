<?php
/*
Plugin Name:  TUP Age Consent
Description: Redirects users to a custom page to consent that their age is 18+.
Version: 1.0
Author: teamup.lk
*/

if (!defined('ABSPATH')) {
    exit;
}

// Define configurable values
define('TUP_AGE_CONSENT_EXCLUDED_PAGES', array('/age-verification/', '/tup_age_consent/?tup_age_consent=yes'));
define('TUP_AGE_CONSENT_COOKIE_NAME', 'tup_age_consent');
define('TUP_AGE_CONSENT_COOKIE_EXPIRY', 30 * DAY_IN_SECONDS);
define('TUP_AGE_CONSENT_REDIRECT_PAGE', '/age-verification/');
define('TUP_AGE_CONSENT_CONSENT_URL', '/tup_age_consent/?tup_age_consent=yes');

add_action('init', 'tup_check_age_consent');

function tup_check_age_consent() {
    if (is_admin() || wp_doing_ajax()) return;

    $excluded_pages = TUP_AGE_CONSENT_EXCLUDED_PAGES;

    $matched = false;
    foreach ($excluded_pages as $page) {
        if (preg_match("#^" . preg_quote($page, '#') . "#", $_SERVER['REQUEST_URI'])) {
            $matched = true;
            break;
        }
    }

    if (!isset($_COOKIE[TUP_AGE_CONSENT_COOKIE_NAME]) && !$matched) {
        wp_redirect(home_url(TUP_AGE_CONSENT_REDIRECT_PAGE . '?redirect=' . urlencode($_SERVER['REQUEST_URI'])));
        exit;
    }
}

add_action('init', 'tup_handle_consent_url');
function tup_handle_consent_url() {
    if (isset($_GET['tup_age_consent']) && $_GET['tup_age_consent'] == 'yes') {
        setcookie(TUP_AGE_CONSENT_COOKIE_NAME, '1', time() + TUP_AGE_CONSENT_COOKIE_EXPIRY, defined('COOKIEPATH') ? COOKIEPATH : '/', defined('COOKIE_DOMAIN') ? COOKIE_DOMAIN : '');        
        // Redirect to the home page or any other page
        wp_redirect(home_url());
        exit;
    }
}

function tup_get_consent_url() {
    return home_url(TUP_AGE_CONSENT_CONSENT_URL);
}
