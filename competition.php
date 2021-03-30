<?php
/**
 * @package Competition
 * @version 1.0.0
 */
/*
Plugin Name: Competition
Description: Demo plugin
Author: ImI S
Version: 1.0.0
*/
if (!defined('ABSPATH')) {
    return;
}

/**
 * ----------------------
 * Basic routes
 * ----------------------
 */
define('APP_ROOT', plugin_dir_path(__FILE__));
define('APP_URL', plugin_dir_url(__FILE__));
define('SITE_URL', get_site_url());

/**
 * ---------------------
 * AJAX Responses
 * ---------------------
 */
define('AJAX_SAVED', __('Competition has been saved.'));
define('AJAX_NOT_SAVED', __('Competition has NOT been saved. Please, try again.'));
define('AJAX_UPDATED', __('Competition has been updated.'));
define('AJAX_NOT_UPDATED', __('Competition has NOT been updated. Please, try again.'));
define('AJAX_NOT_FOUND', __('The data of the competition has NOT been found in the database. Please, try again.'));
define('AJAX_DELETED', __('Competition has been deleted.'));
define('AJAX_NOT_DELETED', __('The Competition has NOT been deleted. Please, try again.'));
define('AJAX_UPLOAD_ERROR', __('The upload of entry codes has been failed. Please, try again.'));
define('AJAX_CODES_NOT_DELETED', __('The entry codes have not been deleted. Please contact the administrator'));
define('AJAX_UNKNOWN_ERROR', __('An unknown error has occured. Please, try again or contact the administrator.'));
define('AJAX_DENIED', __('Access Denied'));

/**
 * ----------------------
 * DB PREFIX
 * ----------------------
 */

global $wpdb;
define('DB_PREFIX', $wpdb->prefix);

/**
 * -------------------
 * Debug
 * -------------------
 */
require_once(APP_ROOT . 'classes/Debug.php');
new Debug;

/**
 * ---------------------------
 * Manage Database Operations
 * ---------------------------
 */
require_once(APP_ROOT . 'classes/Data.php');


/**
 * ---------------------
 * Activation
 * ---------------------
 */
function comp_activate()
{
    global $wp_version;

    if (version_compare($wp_version, '5.6', '<')) {
        wp_die(esc_html_e('This plugin requires at least version 5.6 .', 'competition'));
    }

    /** Creation of db tables */
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Table names
    $table_competitions = $wpdb->prefix . 'comp_competitions';
    $table_codes = $wpdb->prefix . 'comp_codes';

    // Queries
    $sql_competitions = 'CREATE TABLE IF NOT EXISTS ' . $table_competitions . ' (
        comp_id int(7) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        comp_title varchar(255) NOT NULL,
        comp_content text NOT NULL      
    );';

    $sql_codes = 'CREATE TABLE IF NOT EXISTS ' . $table_codes . ' (
        code_id int(7) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        comp_id int(7) NOT NULL,
        entry_id int(7) NOT NULL,
        entry_code varchar(255) NOT NULL,
        entry_date datetime,
        entry_email varchar(255)             
    );';

    $sqls = [$sql_competitions, $sql_codes];
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    foreach ($sqls as $query) {
        dbDelta($query);
    }
}

register_activation_hook(__FILE__, 'comp_activate');

/**
 * -------------------
 * Menu
 * -------------------
 */
function comp_create_menu()
{
    add_menu_page('Competition', 'Competition', 'manage_options', 'comp_main_page', 'comp_main_page', '', 2);
    add_submenu_page('comp_main_page', 'Add Competition', 'Add Competition', 'manage_options', 'comp_set_competition', 'comp_set_competition');
}

add_action('admin_menu', 'comp_create_menu');

/**
 * --------------------
 * Custom files
 * --------------------
 */
function custom_files()
{
    /** Styles */
    // W3CSS
    wp_register_style('comp_w3css', APP_URL . 'libs/w3.css');
    wp_enqueue_style('comp_w3css');

    // style.css
    wp_register_style('comp_custom_style', APP_URL . 'style/style.css');
    wp_enqueue_style('comp_custom_style');

    // DataTables
    wp_register_style('comp_datatables_style', APP_URL . 'libs/datatables/datatables.min.css');
    wp_enqueue_style('comp_datatables_style');


    /** JavaScript */
    // jQuery
    wp_register_script('comp_jquery', APP_URL . 'libs/jquery-3.5.1.min.js');
    wp_enqueue_script('comp_jquery');

    // Modal
    wp_register_script('comp_competitions', APP_URL . 'js/competitions.js');
    wp_enqueue_script('comp_competitions');

    // Validation
    wp_register_script('comp_validation', APP_URL . 'js/validation.js');
    wp_enqueue_script('comp_validation');

    // Ajax
    wp_register_script('comp_ajax', APP_URL . 'js/ajax.js');
    wp_enqueue_script('comp_ajax');

    // DataTables
    wp_register_script('comp_datatables', APP_URL . 'libs/datatables/datatables.min.js');
    wp_enqueue_script('comp_datatables');
    
    // Config
    wp_register_script('comp_config', APP_URL . 'js/config.js');
    wp_enqueue_script('comp_config');
}

add_action('admin_enqueue_scripts', 'custom_files');

/**
 * -------------------
 * Pages
 * -------------------
 */
require_once(APP_ROOT . 'functions/comp_main_page_fn.php');
require_once(APP_ROOT . 'functions/comp_set_competition_fn.php');
require_once(APP_ROOT . 'functions/comp_shortcode.php');

/**
 * -------------------
 * Deactivation
 * -------------------
 */
function comp_deactivate()
{
    remove_menu_page('comp_main_menu');
}

register_deactivation_hook(__FILE__, 'comp_deactivate');

/**
 * -------------------
 * Uninstall
 * -------------------
 */
function comp_remove_plugin()
{

    /** Removal of db tables */
    global $wpdb;

    // Table names
    $table_names = [
        $wpdb->prefix . 'comp_competitions',
        $wpdb->prefix . 'comp_codes'
    ];

    foreach($table_names as $table) {
        $wpdb->query("DROP TABLE IF EXISTS $table");
    }
}

register_uninstall_hook(__FILE__, 'comp_remove_plugin');
