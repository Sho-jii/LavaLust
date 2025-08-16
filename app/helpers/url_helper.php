<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

if (!function_exists('site_url')) {
    function site_url($uri = '') {
        $LAVA =& lava_instance();
        return $LAVA->config->get('base_url') . ltrim($uri, '/');
    }
}

if (!function_exists('redirect')) {
    function redirect($uri = '', $method = 'auto', $code = 302) {
        if (!preg_match('#^(\w+:)?//#i', $uri)) {
            $uri = site_url($uri);
        }

        switch ($method) {
            case 'refresh':
                header('Refresh:0;url=' . $uri);
                break;
            default:
                header('Location: ' . $uri, TRUE, $code);
                break;
        }
        exit;
    }
}
?>
