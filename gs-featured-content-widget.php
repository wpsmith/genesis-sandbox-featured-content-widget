<?php
/**
 * Plugin Name: Genesis Sandbox Featured Content Widget
 * Plugin URI: https://wpsmith.net/
 * Description: Based on the Genesis Featured Widget Amplified for additional functionality which allows support for custom post types, taxonomies, and extends the flexibility of the widget via action hooks to allow the elements to be re-positioned or other elements to be added.
 * Version: 1.1.8
 * Author: Travis Smith
 * Author URI: http://wpsmith.net/
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume 
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 */
 
/**
 * Genesis Sandbox Featured Post Widget
 *
 * @category   Genesis_Sandbox_Featured_Content
 * @package    Widgets
 * @author     Travis Smith
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link       http://wpsmith.net/
 * @since      1.1.0
 */

/** Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) exit( 'Cheatin&#8217; uh?' );

define( 'GSFC_PLUGIN_NAME', basename( dirname( __FILE__ ) ) );
define( 'GSFC_PLUGIN_VERSION', '1.1.0' );

/** Load textdomain for translation */
load_plugin_textdomain( 'gsfc', false, basename( dirname( __FILE__ ) ) . '/languages/' );

// register_activation_hook( __FILE__, 'gsfc_activation_check' );
/**
 * Checks for minimum Genesis Theme version before allowing plugin to activate
 *
 * @uses genesis_truncate_phrase()
 */
function gsfc_activation_check() {

    $latest = '2.0';

    if ( basename( get_template_directory() ) != 'genesis' ) {
        deactivate_plugins( plugin_basename( __FILE__ ) ); // Deactivate ourself
        wp_die( sprintf( __( 'Sorry, you can\'t activate unless you have installed %1$sGenesis%2$s', 'gsfc' ), '<a href="http://wpsmith.net/get-genesis/">', '</a>' ) );
    }
    
    if ( ! function_exists( 'genesis_upgrade_2001' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) ); // Deactivate ourself
        wp_die( sprintf( __( 'Sorry, you can\'t activate without %1$sGenesis %2$s%3$s or greater', 'gsfc' ), '<a href="http://wpsmith.net/get-genesis/">', $latest, '</a>' ) );
    }
}

add_action( 'genesis_init', 'gsfc_init', 50 );
/**
 * Initializes Widget & Admin Settings
 * @since 1.1.0
 */
function gsfc_init() {
    if ( is_admin() ) {
        require_once( 'gsfc-settings.php' );
        
        global $_gsfc_settings;
        $_gsfc_settings = new GSFC_Settings();
    }
    
}

require_once( 'widget.php' );
add_action( 'widgets_init', 'gsfc_widgets_init', 50 );
/**
 * Register GSFC for use in the Genesis theme.
 *
 * @since 1.1.0
 */
function gsfc_widgets_init() {
    if ( class_exists( 'Premise_Base' ) && !is_admin() ) {
        return;
    }
    $gfwa = genesis_get_option( 'gsfc_gfwa' );
    if ( class_exists( 'Genesis_Featured_Widget_Amplified' ) && $gfwa ) {
        unregister_widget( 'Genesis_Featured_Widget_Amplified' );
    }
    register_widget( 'GS_Featured_Content' );
}

add_filter( 'plugin_action_links', 'gsfc_action_links', 10, 2 );
/**
 * Add Menus & Donate Action Link.
 * 
 * @param array $links Array of links.
 * @param string $file Basename of plugin.
 * @return array $links Maybe modified array of links.
 */
function gsfc_action_links( $links, $file ) {
    if ( $file == plugin_basename( __FILE__ ) ) {
        if ( class_exists( 'Genesis_Featured_Widget_Amplified' ) )
            array_unshift( $links, sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=genesis' ), __( 'Settings', 'gsfc' ) ) );
        array_unshift( $links, sprintf( '<a href="%s">%s</a>', admin_url( 'widgets.php' ), __( 'Widgets', 'gsfc' ) ) );
        array_push( $links, sprintf( '<a href="http://wpsmith.net/donation" target="_blank">%s</a>', __( 'Donate', 'gsfc' ) ) );
    }
    return $links;
}

add_action( 'save_post', 'gsfc_save_post', 10, 3 );
/**
 * Hooks into save_post to remove all GSFC Transients.
 *
 * Contains a filter gsfc_save_post_query for anyone to modify the query.
 *
 * @since  1.1.5
 * @date   2014-06-24
 * @author Travis Smith <t(at)wpsmith.net>}
 *
 * @param  int            $post_ID                Post ID.
 * @param  WP_Post        $post                   Post object.
 * @param  bool           $update                 Whether post is being updated
 */
function gsfc_save_post( $post_ID, $post, $update ) {
    global $wpdb;

    $query = apply_filters( 'gsfc_save_post_query', "DELETE FROM $wpdb->options WHERE 'option_name' LIKE '%transient_gsfc%'", $post_ID, $post, $update );
    $wpdb->query( $query );

}