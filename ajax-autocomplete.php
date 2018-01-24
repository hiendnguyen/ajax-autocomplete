<?php
/*
 * Plugin Name: Ajax Autocomplete
 * Plugin URI: https://github.com/hiendnguyen/ajax-autocomplete
 * Description: DEMO Wordpress Ajax jQuery textbox autocomplete, data source from custom table. Put [test-form] shortcode to any post or page to test it out.
 * Version: 0.1
 * Author: Hien D. Nguyen
 * Author URI: https://vndeveloper.com/
 * License: GPL3+
 */

if(!defined('ABSPATH')) exit;

// Plugin activation: Create sample db table and insert test data
register_activation_hook(basename(dirname(__FILE__)) . '/' . basename( __FILE__ ), 'activate');
function activate() {
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	$table = $wpdb->prefix . 'testing_table';
	$query = "CREATE TABLE IF NOT EXISTS {$table} (
		id INT NOT NULL AUTO_INCREMENT,
		name VARCHAR(128),
		PRIMARY KEY  (id)
	)$charset_collate;";
	dbDelta($query);

	// Insert test data
	$wpdb->insert($table, array('name'=>'LogiTech'));
	$wpdb->insert($table, array('name'=>'Apple'));
	$wpdb->insert($table, array('name'=>'Dell'));
	$wpdb->insert($table, array('name'=>'Google'));

}

// Enqueue js, css
add_action('wp_enqueue_scripts', 'mysite_js');
function mysite_js() {
	$wp_scripts = wp_scripts();
	wp_register_style('mysitecss', plugins_url('/assets/css/frontend.css', __FILE__));
	wp_enqueue_style('mysitecss' );	
	wp_enqueue_style('jquery_ui_css','http://ajax.googleapis.com/ajax/libs/jqueryui/' . $wp_scripts->registered['jquery-ui-core']->ver . '/themes/flick/jquery-ui.css');
	wp_enqueue_script('mysitejs', plugins_url('/assets/js/frontend.js', __FILE__), array('jquery', 'jquery-ui-core', 'jquery-ui-autocomplete'));
	wp_localize_script('mysitejs', 'ajax_object', array('ajax_url' => admin_url( 'admin-ajax.php')));
}

add_shortcode('test-form', 'test_form');
function test_form($args, $content=''){
	ob_start();
	include(plugin_dir_path(__FILE__) . 'includes/forms/test-form.php');
	return ob_get_clean();
}

add_action('wp_ajax_nopriv_myautocomplete', 'ajax_autocomplete');
add_action('wp_ajax_myautocomplete', 'ajax_autocomplete'); 
function ajax_autocomplete() {
	global $wpdb;
	$table = $wpdb->prefix . 'testing_table';
	$results = $wpdb->get_results("SELECT name FROM " . $table . " WHERE name LIKE '%" . $_POST['keyword'] . "%'");
	$items = array();
	if ( !empty( $results) ) {
		foreach ( $results as $result ) {
			$items[] = $result->name;
		}
	}
	echo json_encode($items);
	die();
}

