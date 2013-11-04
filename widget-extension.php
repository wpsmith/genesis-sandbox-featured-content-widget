<?php

/**
 * Genesis Sandbox Featured Content Widget Extension Class
 *
 * @category   Genesis_Sandbox
 * @package    Widgets
 * @author     Travis Smith
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link       http://wpsmith.net/
 * @since      1.1.0
 */

if ( ! class_exists( 'GSFC_Skeleton' ) ) {
class GSFC_Skeleton {
    public function __construct() {
        if ( class_exists( 'GS_Featured_Content' ) && class_exists( 'WPSS_Font_Awesome' ) ) {
            add_filter( 'gsfc_defaults', array( $this, 'defaults' ), 10, 3 );
            add_filter( 'gsfc_form_fields', array( $this, 'add_form_fields' ), 10, 3 );
            add_filter( 'gsfc_update', array( $this, 'update' ), 10, 3 );
            
            add_action( 'gsfc_before_post_content', array( $this, 'do_action' ) );
            add_action( 'gsfc_post_content', array( $this, 'do_action' ) );
            add_action( 'gsfc_after_post_content', array( $this, 'do_action' ) );
        }
    }
    
    /**
     * Add Font Awesome default settings to Featured Content Widget
     * 
     * @param array $defaults Array of default settings.
     * @return array $defaults Modified array of default settings.
     */
    public function defaults( $defaults ) {
        $gsfc_defaults = array();
        
        // Give precendent to existing defaults over my own
        return wp_parse_args( $defaults, $gsfc_defaults );
    }
    
    /**
     * Add Font Awesome to Featured Content Widget
     * 
     * @param array $columns Array of Columns, Boxes, & Form Fields
     * @param array $instance The settings for the particular instance of the widget.
     * @param array $boxes Array of the Form Field Boxes
     * @return array $icons Alpha sorted list of available Font Awesome classes
     */
    public function add_form_fields( $columns, $instance, $boxes ) {
        $box_8 = array();
        
        $columns['col2'] = array( $box_8, $boxes['box_5'], $boxes['box_6'], $boxes['box_7'], );
        
        return $columns;
    }
    
    /**
	 * Update a particular instance.
	 *
	 * This function should check that $new_instance is set correctly.
	 * The newly calculated value of $instance should be returned.
	 * If "false" is returned, the instance won't be saved/updated.
	 *
	 * @since 0.1.8
	 *
	 * @param array $new_instance New settings for this instance as input by the user via form()
	 * @param array $old_instance Old settings for this instance
	 * @return array Settings to save or bool false to cancel saving
	 */
	public function update( $new_instance, $old_instance ) {
        return $new_instance;
    }
    
    /**
     * Do Action
     *
     * @param array $instance The settings for the particular instance of the widget.
     */
    public function do_action( $instance ) {
        //* Bail if empty show param
        if ( empty( $instance['show_icon'] ) ) return;
        
        $link = $instance['link_icon_field'] && get_post_meta( get_the_ID(), $instance['link_icon_field'], true ) ? get_post_meta( get_the_ID(), $instance['link_icon_field'], true ) : get_permalink();
        $icon = sprintf( '<%1$s class="%2$s"></%1$s>', $instance['icon_tag'], $instance['icon'] );
        $icon = $instance['link_icon'] == 1 ? sprintf( '<a href="%s" title="%s" class="%s">%s</a>', $link, the_title_attribute( 'echo=0' ), $align, $icon ) : $icon;
        echo 'icon';
        GS_Featured_Content::maybe_echo( $instance, 'gsfc_before_post_content', 'icon_position', 'before-title', $icon );
        GS_Featured_Content::maybe_echo( $instance, 'gsfc_post_content', 'icon_position', 'after-title', $icon );
        GS_Featured_Content::maybe_echo( $instance, 'gsfc_after_post_content', 'icon_position', 'after-content', $icon );
    }
}
}