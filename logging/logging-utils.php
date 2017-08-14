<?php
/**
 * Function that allows printing in a nice formatted fashion to the debug log.
 * Please note you need to enable this log wp-config.php
 * My settings are
 * 
 * define('WP_DEBUG', true);
 * define('WP_DEBUG_LOG', true);
 * define('WP_DEBUG_DISPLAY', false);
 * 
 * @see http://codex.wordpress.org/Debugging_in_WordPress
 */
if (!function_exists('write_log')) {
    function write_log ( $log )  {
        if ( true === WP_DEBUG ) {
            if ( is_array( $log ) || is_object( $log ) ) {
                error_log( print_r( $log, true ) );
            } else {
                error_log( $log );
            }
        }
    }
}