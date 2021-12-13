<?php

/*
Plugin Name: Custom Post Type Items
Plugin URI: https://creiden.com/
Description: Plugin for making custom post type items and rest APIs
Author: Amr Degheidy
Author URI: https://creiden.com/
version: 1.0.2
*/

define( 'GA_CPTI_VERSION', '1.0.2' );

include_once( plugin_dir_path( __FILE__ ) . '/helper/Helper.php' );
include_once( plugin_dir_path( __FILE__ ) . '/classes/Item.php' );

Item::register();