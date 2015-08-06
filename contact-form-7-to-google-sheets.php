<?php

/**
 * Plugin Name:       Contact Form 7 to Google Sheets
 * Plugin URI:        http://dannyconnolly.me/
 * Description:       Save posted data to Google Sheets
 * Version:           1.0.0
 * Author:            Danny Connolly
 * Author URI:        http://dannyconnolly.me/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       contact-form-7-to-google-sheets
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}


define('CFGS_URL', WP_PLUGIN_URL . "/" . dirname(plugin_basename(__FILE__)));
define('CFGS_PATH', WP_PLUGIN_DIR . "/" . dirname(plugin_basename(__FILE__)));

define("CFGS_LOG_FILE", "cfgs.log");

/*
 * Requires libraries for Google Spreadsheet and Google Client
 */
require CFGS_PATH . '/vendor/autoload.php';

if (!class_exists('CF7GS_Google_Spreadsheet')) {
    require_once( CFGS_PATH . '/inc/class.cf7gs-google-spreadsheet.php' );
}

if (!class_exists('CFGS')) {
    require_once( CFGS_PATH . '/inc/class.contact-form-7-to-google-sheets.php' );
}

function dump_res($d) {
    echo '<pre>';
    var_dump($d);
    echo '</pre>';
}

new CFGS();
