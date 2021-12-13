<?php
/**
 * Class Helper
 *
*/

if ( ! defined( 'ABSPATH' ) ) {
    die( '-1' );
}

class Helper
{
    public static function authentication($username, $password)
    {
        $user = wp_authenticate($username, $password);

        if (isset($user->errors)) {
            
            return null;
        }
        
        return $user->data;
    }
}