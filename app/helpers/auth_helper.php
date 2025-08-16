<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

if (!function_exists('is_logged_in')) {
    function is_logged_in() {
        return isset($_SESSION['user_id']);
    }
}

if (!function_exists('is_admin')) {
    function is_admin() {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
}

if (!function_exists('require_login')) {
    function require_login() {
        if (!is_logged_in()) {
            $_SESSION['redirect_url'] = current_url();
            redirect('/login');
            exit;
        }
    }
}

if (!function_exists('require_admin')) {
    function require_admin() {
        require_login();
        if (!is_admin()) {
            redirect('/');
            exit;
        }
    }
}

if (!function_exists('current_user_id')) {
    function current_user_id() {
        return $_SESSION['user_id'] ?? null;
    }
}

if (!function_exists('current_user_name')) {
    function current_user_name() {
        return $_SESSION['user_name'] ?? 'Guest';
    }
}

if (!function_exists('current_url')) {
    function current_url() {
        return $_SERVER['REQUEST_URI'];
    }
}
