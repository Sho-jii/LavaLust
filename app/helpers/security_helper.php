<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

if (!function_exists('html_escape')) {
    function html_escape($var, $double_encode = TRUE) {
        if (empty($var)) {
            return $var;
        }

        if (is_array($var)) {
            foreach (array_keys($var) as $key) {
                $var[$key] = html_escape($var[$key], $double_encode);
            }
            return $var;
        }

        return htmlspecialchars($var, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', $double_encode);
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field() {
        $LAVA =& lava_instance();
        $token_name = $LAVA->config->get('csrf_token_name');
        $token_value = $LAVA->security->get_csrf_hash();
        
        return '<input type="hidden" name="' . $token_name . '" value="' . $token_value . '">';
    }
}
?>
