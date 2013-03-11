<?php
/**
 * Media Library Assistant Plugin Loader
 *
 * Defines constants and loads all of the classes and functions required to run the plugin.
 * This file is only loaded if the naming conflict tests in index.php are passed.
 *
 * @package Media Library Assistant
 * @since 0.20
 */

if (!defined('MLA_OPTION_PREFIX'))
	/**
	 * Gives a unique prefix for plugin options; can be set in wp-config.php
	 */
	define('MLA_OPTION_PREFIX', 'mla_');

/**
 * Accumulates error messages from name conflict tests
 *
 * @since 1.14
 */
$mla_plugin_loader_error_messages = '';
 
/**
 * Displays version conflict error messages at the top of the Dashboard
 *
 * @since 1.14
 */
function mla_plugin_loader_reporting_action () {
	global $mla_plugin_loader_error_messages;
	
	echo '<div class="error"><p><strong>The Media Library Assistant cannot load.</strong></p>'."\r\n";
	echo "<ul>{$mla_plugin_loader_error_messages}</ul>\r\n";
	echo '<p>You must resolve these conflicts before this plugin can safely load.</p></div>'."\r\n";
}

/*
 * Basic library of run-time tests.
 */
require_once( MLA_PLUGIN_PATH . 'tests/class-mla-tests.php' );

$mla_plugin_loader_error_messages .= MLATest::min_php_version( '5.2' );
$mla_plugin_loader_error_messages .= MLATest::min_WordPress_version( '3.3' );

if ( ! empty( $mla_plugin_loader_error_messages ) ) {
	add_action( 'admin_notices', 'mla_plugin_loader_reporting_action' );
}
else {
	add_action( 'init', 'MLATest::initialize' );
	
	/*
	 * Template file and database access functions.
	 */
	require_once( MLA_PLUGIN_PATH . 'includes/class-mla-data.php' );
	add_action( 'init', 'MLAData::initialize' );
	
	/*
	 * Custom Taxonomies and WordPress objects.
	 */
	require_once( MLA_PLUGIN_PATH . 'includes/class-mla-objects.php' );
	add_action('init', 'MLAObjects::initialize');
	
	/*
	 * Shortcodes
	 */
	require_once( MLA_PLUGIN_PATH . 'includes/class-mla-shortcodes.php');
	add_action('init', 'MLAShortcodes::initialize');
	
	/*
	 * WordPress 3.5 and later Edit Media screen additions, e.g., meta boxes
	 */
	if ( version_compare( get_bloginfo( 'version' ), '3.5', '>=' ) ) {
		require_once( MLA_PLUGIN_PATH . 'includes/class-mla-edit-media.php');
		add_action('init', 'MLAEdit::initialize');
	}
	
	/*
	 * WordPress 3.5 and later Media Manager (Modal window) additions
	 */
/*	if ( version_compare( get_bloginfo( 'version' ), '3.5', '>=' ) ) {
		require_once( MLA_PLUGIN_PATH . 'includes/class-mla-media-modal.php');
		add_action('init', 'MLAModal::initialize');
	} // */
	
	/*
	 * Plugin settings management
	 */
	require_once( MLA_PLUGIN_PATH . 'includes/class-mla-options.php' );
	add_action( 'init', 'MLAOptions::initialize' );
	 
	/*
	 * Plugin settings management page
	 */
	require_once( MLA_PLUGIN_PATH . 'includes/class-mla-settings.php' );
	add_action( 'init', 'MLASettings::initialize' );
	 
	/*
	 * Custom list table package that extends the core WP_List_Table class.
	 * Doesn't need an initialize function; has a constructor.
	 */
	require_once( MLA_PLUGIN_PATH . 'includes/class-mla-list-table.php' );
	
	/*
	 * Main program
	 */
	require_once( MLA_PLUGIN_PATH . 'includes/class-mla-main.php');
	add_action('init', 'MLA::initialize');
}
?>