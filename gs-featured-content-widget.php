<?php

/**
 * Plugin Name: Genesis Sandbox Featured Content Widget
 * Plugin URI: https://wpsmith.net/
 * Description: Based on Nick Croft's Genesis Featured Widget Amplified for additional functionality which allows support for custom post types, taxonomies, and extends the flexibility of the widget via action hooks to allow the elements to be re-positioned or other elements to be added.
 * Version: 1.0.0
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
 * @category   Genesis_Sandbox
 * @package    Widgets
 * @author     Travis Smith
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link       http://wpsmith.net/
 * @since      1.1.0
 */

/** Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) exit( 'Cheatin&#8217; uh?' );

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

    $theme_info = get_theme_data( TEMPLATEPATH . '/style.css' );

    if ( basename( TEMPLATEPATH ) != 'genesis' ) {
        deactivate_plugins( plugin_basename( __FILE__ ) ); // Deactivate ourself
        wp_die( sprintf( __( 'Sorry, you can\'t activate unless you have installed %1$sGenesis%2$s', 'gfwa' ), '<a href="http://wpsmith.net/get-genesis/">', '</a>' ) );
    }
    
    if ( function_exists( 'genesis_truncate_phrase' ) )
        $version = genesis_truncate_phrase( $theme_info['Version'], 3 );

    if ( version_compare( $version, $latest, '<' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) ); // Deactivate ourself
        wp_die( sprintf( __( 'Sorry, you can\'t activate without %1$sGenesis %2$s%3$s or greater', 'gsfc' ), '<a href="http://wpsmith.net/get-genesis/">', $latest, '</a>' ) );
    }
}

add_action( 'widgets_init', 'gs_load_widgets' );
/**
 * Register widgets for use in the Genesis theme.
 *
 * @since 1.7.0
 */
function gs_load_widgets() {
	register_widget( 'GS_Featured_Content' );
}

/**
 * Genesis Sandbox Featured Post widget class.
 *
 * @since 0.1.8
 *
 * @category   Genesis_Sandbox
 * @package    Widgets
 */
class GS_Featured_Content extends WP_Widget {
    
    /**
     * Holds a copy of the object for easy reference.
     *
     * @since 1.0.0
     *
     * @var object
     */
    static $widget_instance;
    
	/**
	 * Holds widget settings defaults, populated in constructor.
	 *
	 * @var array
	 */
	protected $defaults;

	/**
	 * Constructor. Set the default widget options and create widget.
	 *
	 * @since 0.1.8
	 */
	function __construct() {
        GS_Featured_Content::$widget_instance = $this;
		$this->defaults = array(
            'add_column_classes'      => 0,
            'archive_link'            => '',
            'class'                   => '',
            'column_classes'          => '',
            'content_limit'           => '',
            'count'                   => 0,
            'custom_field'            => '',
            'excerpt_cutoff'          => '&hellip;',
            'excerpt_limit'           => 55,
            'exclude_cat'             => '',
            'exclude_displayed'       => 0,
            'exclude_terms'           => '',
            'extra_format'            => 'ul',
            'extra_num'               => '',
            'extra_num'               => '',
            'extra_posts'             => '',
            'extra_title'             => '',
            'extra_title'             => '',
            'gravatar_alignment'      => '',
            'gravatar_size'           => '',
            'image_alignment'         => '',
            'image_position'          => 'before-title',
            'image_size'              => '',
            'include_exclude'         => '',
            'link_gravatar'           => 0,
            'link_image'              => 1,
            'link_image_field'        => '',
            'link_title'              => 1,
            'link_title_field'        => '',
            'meta_key'                => '',
            'more_from_category'      => '',
            'more_from_category_text' => __( 'More Posts from this Category', 'gsfc' ),
            'more_text'               => __( '[Read More...]', 'gsfc' ),
            'optimize'                => 1,
            'order'                   => '',
            'orderby'                 => '',
            'page_id'                 => '',
            'paged'                   => '',
            'post_align'              => '',
            'post_id'                 => '',
            'post_info'               => '[post_date] ' . __( 'By', 'gsfc' ) . ' [post_author_posts_link] [post_comments]',
            'post_meta'               => '[post_categories] [post_tags]',
            'post_type'               => 'post',
            'posts_cat'               => '',
            'posts_num'               => 1,
            'posts_offset'            => 0,
            'posts_term'              => '',
            'show_archive_line'       => 0,
            'show_byline'             => 0,
            'show_content'            => 'excerpt',
            'show_gravatar'           => 0,
            'show_image'              => 0,
            'show_paged'              => '',
            'show_sticky'             => '',
            'show_title'              => 0,
            'title'                   => '',
            'title_cutoff'            => '&hellip;',
            'title_limit'             => '',
		);

		$widget_ops = array(
			'classname'   => 'featured-content',
			'description' => __( 'Displays featured posts with thumbnails', 'gsfc' ),
		);

		$control_ops = array(
			'id_base' => 'featured-content',
			'width'   => 505,
			'height'  => 350,
		);
        
        $name = __( 'Genesis Sandbox', 'gsfc' );
        if ( defined( 'CHILD_NAME' ) && true === apply_filters( 'gsfc_widget_name', false ) )
            $name = CHILD_THEME_NAME;
        elseif ( apply_filters( 'gsfc_widget_name', false ) )
            $name = apply_filters( 'gsfc_widget_name', false );

		parent::__construct( 'featured-content', sprintf( __( '%s - Featured Content', 'gsfc' ), $name ), $widget_ops, $control_ops );
        
        
        GS_Featured_Content::add();
        do_action( 'gs_featured_content_actions', $this );
	}
    
    /**
     * Adds all Widget's Actions at once for easy removal.
     */
    public function add() {
        add_filter( 'post_class', array( 'GS_Featured_Content', 'post_class' ) );
        add_filter( 'excerpt_length', array( 'GS_Featured_Content', 'excerpt_length' ) );
        add_filter( 'excerpt_more', array( 'GS_Featured_Content', 'excerpt_more' ) );
        
        //* Do Post Image
        add_action( 'gsfc_before_post_content', array( 'GS_Featured_Content', 'do_post_image' ) );
        add_action( 'gsfc_post_content', array( 'GS_Featured_Content', 'do_post_image' ) );
        add_action( 'gsfc_after_post_content', array( 'GS_Featured_Content', 'do_post_image' ) );
        
        //* Do before widget post content
        add_action( 'gsfc_before_post_content', array( 'GS_Featured_Content', 'do_gravatar' ) );
        add_action( 'gsfc_before_post_content', array( 'GS_Featured_Content', 'do_post_title' ) );
        add_action( 'gsfc_before_post_content', array( 'GS_Featured_Content', 'do_byline' ) );
        
        //* Do widget post content
        add_action( 'gsfc_post_content', array( 'GS_Featured_Content', 'do_post_content' ) );
        
        //* Do after widget post content
        add_action( 'gsfc_after_post_content', array( 'GS_Featured_Content', 'do_post_meta' ) );
        
        //* Do after loop
        add_action( 'gsfc_endwhile', array( 'GS_Featured_Content', 'do_posts_nav' ) );
        
        //* Do after loop reset
        add_action( 'gsfc_after_loop_reset', array( 'GS_Featured_Content', 'do_extra_posts' ) );
        add_action( 'gsfc_after_loop_reset', array( 'GS_Featured_Content', 'do_more_from_category' ) );
        
        //* Scripts
        add_action( 'admin_print_footer_scripts', array( 'GS_Featured_Content', 'form_submit' ) );
        add_action( 'wp_enqueue_scripts', array( 'GS_Featured_Content', 'enqueue_style' ) );
    }
    
    /**
     * Adds all Widget's Actions at once for easy removal.
     *
     * @param int $length Current excerpt length.
     * @return int Maybe new excerpt length.
     */
    public function excerpt_more( $length ) {
        if ( GS_Featured_Content::has_value( 'excerpt_cutoff' ) )
            return GS_Featured_Content::$widget_instance['excerpt_cutoff'];
        return $length;
    }
    
    /**
     * Adds all Widget's Actions at once for easy removal.
     *
     * @param int $length Current excerpt length.
     * @return int Maybe new excerpt length.
     */
    public function excerpt_length( $length ) {
        if ( GS_Featured_Content::has_value( 'excerpt_limit' ) )
            return (int)GS_Featured_Content::$widget_instance['excerpt_limit'];
        return $length;
    }
    
    /**
     * Adds all Widget's Actions at once for easy removal.
     */
    public function enqueue_style() {
        if ( is_admin() ) return;
        $suffix = ( defined( 'WP_DEBUG' ) || defined( 'SCRIPT_DEBUG' ) ) ? '.css' : '.min.css';
        $src    = CHILD_LIB . '/widgets/assets/column-classes' . $suffix;
        if ( function_exists( 'gs_enqueue_dep_stylesheet' ) ) {
            gs_enqueue_dep_stylesheet( $src );
        } else {
            $version = defined( 'CHILD_THEME_VERSION' ) && CHILD_THEME_VERSION ? CHILD_THEME_VERSION : PARENT_THEME_VERSION;
            $deps    = defined( 'CHILD_THEME_NAME' ) && CHILD_THEME_NAME ? sanitize_title_with_dashes( CHILD_THEME_NAME ) : 'child-theme';
            
            wp_enqueue_style( 'column-classes', $src, array( $deps, ), $version );
        }
    }
    
    /**
     * Getter method for retrieving the object instance.
     *
     * @since 1.0.0
     */
    public static function get_instance() {
    
        return GS_Featured_Content::$widget_instance;
    
    }
    
    /**
     * Determines whether $instance option isset & has a value
     * 
     * @param string $opt Instance option.
     * 
     * @return bool True if option has value
     */
    protected static function has_value( $opt ) {
        if( is_array( GS_Featured_Content::$widget_instance ) && isset( GS_Featured_Content::$widget_instance[ $opt ] ) && GS_Featured_Content::$widget_instance[ $opt ] )
            return true;
        return false;
    }
    
    /**
     * Returns Column Class Number
     * 
     * @param string $class Column Class.
     * 
     * @return int Column Class Integer
     */
    public static function get_col_class_num( $class ) {
        switch( $class ) {
            case 'one-half':
                return 2;
            case 'one-third':
            case 'two-thirds':
                return 3;
            case 'one-fourth':
            case 'three-fourths':
                return 4;
            case 'one-fifth':
            case 'two-fifths':
            case 'three-fifths':
            case 'four-fifths':
                return 5;
            case 'one-sixth':
            case 'five-sixths':
                return 6;
            default:
                return 1;
        }
    }
    
    /**
     *  Adds number class, and odd/even class to widget output
     *
     * @global integer $gs_counter
     * @param array $classes Array of post classes.
     * @return array $classes Modified array of post classes.
     */
    public static function post_class( $classes ) {
        global $gs_counter;
        $classes[] = sprintf( 'gs-%s', $gs_counter + 1 );
        $classes[] = $gs_counter + 1 & 1 ? 'gs-odd' : 'gs-even';
        $classes[] = 'gs-featured-content-entry';
        
        //* First Class
        if ( GS_Featured_Content::has_value( 'column_class' ) && ( 0 == $gs_counter || 0 == $gs_counter % GS_Featured_Content::get_col_class_num( GS_Featured_Content::$widget_instance['column_class'] ) ) )
            $classes[] = 'first';
        
        //* Custom Class
        if ( GS_Featured_Content::has_value( 'class' ) )
            $classes[] = GS_Featured_Content::$widget_instance['class'];
        
        //* Column Class
        if ( GS_Featured_Content::has_value( 'column_class' ) )
            $classes[] = GS_Featured_Content::$widget_instance['column_class'];

        return $classes;
    }
    
    /**
     * Inserts Post Image
     *
     * @param array $instance The settings for the particular instance of the widget.
     */
    public static function do_byline( $instance ) {
        if ( !empty( $instance['show_byline'] ) && !empty( $instance['post_info'] ) )
                printf( '<p class="byline post-info">%s</p>', do_shortcode( esc_html( $instance['post_info'] ) ) );
    }
    
    /**
     * Inserts Post Image
     *
     * @param array $instance The settings for the particular instance of the widget.
     */
    public static function do_post_image( $instance ) {
        //* Bail if empty show param
        if ( empty( $instance['show_image'] ) ) return;
        
        $align = $instance['image_alignment'] ? esc_attr( $instance['image_alignment'] ) : 'alignnone';
        $link = $instance['link_image_field'] && genesis_get_custom_field( $instance['link_image_field'] ) ? genesis_get_custom_field( $instance['link_image_field'] ) : get_permalink();
        
        $image = genesis_get_image( array(
				'format'  => 'html',
				'size'    => $instance['image_size'],
				'context' => 'featured-post-widget',
				'attr'    => genesis_parse_attr( 'entry-image-widget', array( 'class' => $align, ) ),
			) );
        
        $image = $instance['link_image'] == 1 ? sprintf( '<a href="%s" title="%s" class="%s">%s</a>', $link, the_title_attribute( 'echo=0' ), $align, $image ) : $image;
        
        GS_Featured_Content::maybe_echo( $instance, 'gsfc_before_post_content', 'image_position', 'before-title', $image );
        GS_Featured_Content::maybe_echo( $instance, 'gsfc_post_content', 'image_position', 'after-title', $image );
        GS_Featured_Content::maybe_echo( $instance, 'gsfc_after_post_content', 'image_position', 'after-content', $image );
    }
    
    /**
     * Outputs content conditionally based on current filter
     * 
     * @param string $action Action to output.
     * @param string $param Instance choice.
     * @param string $value Value of instance choice.
     * @param mixed $content HTML content to output.
     */
    public static function maybe_echo( $instance, $action, $param, $value, $content ) {
        echo current_filter() == $action && $instance[ $param ] == $value ? $content : '';
    }
    
    /**
     * Do action alias
     *
     * @param string $name Action name.
     * @param array $instance The settings for the particular instance of the widget.
     */
    public static function action( $name, $instance ) {
        do_action( $name, $instance );
    }
    
    /**
     * Do widget framework.
     *
     * @param array $instance The settings for the particular instance of the widget.
     */
    public static function framework( $instance ) {
        global $gs_counter;
        
        genesis_markup( array(
            'html5'   => '<article %s>',
            'xhtml'   => sprintf( '<div class="%s">', implode( ' ', get_post_class() ) ),
            'context' => 'entry',
        ) );

        GS_Featured_Content::action( 'gsfc_before_post_content', $instance );
        GS_Featured_Content::action( 'gsfc_post_content', $instance );
        GS_Featured_Content::action( 'gsfc_after_post_content', $instance );

        $gs_counter++;
        
        genesis_markup( array(
            'html5' => '</article>',
            'xhtml' => '</div>',
        ) );
    
    }
    
    /**
     * Outputs Post Title if option is selects
     *
     * @param array $instance The settings for the particular instance of the widget.
     */
    public static function do_post_title( $instance ) {
        //* Bail if empty show param
        if ( empty( $instance['show_title'] ) ) return;
        
        //* Custom Link or Permalink
        $link = $instance['link_title_field'] && genesis_get_custom_field( $instance['link_title_field']) ? genesis_get_custom_field( $instance['link_title_field']) : get_permalink();
        
        //* Add Link to Title?
        $wrap_open = $instance['link_title'] == 1 ? sprintf( '<a href="%s" title="%s">', $link, the_title_attribute( 'echo=0' ) ) : '';
        $wrap_close = $instance['link_title'] == 1 ? '</a>' : '';

        if ( ! empty( $instance['title_limit'] ) )
            $title = genesis_truncate_phrase( the_title_attribute( 'echo=0' ) , $instance['title_limit'] ) . $instance['title_cutoff'];
        else
            $title = the_title_attribute( 'echo=0' );
        
        if ( genesis_html5() )
            $hclass = ' class="entry-title"';
        else
            $hclass = '';
        
        printf( '<h2%s>%s%s%s</h2>', $hclass, $wrap_open, $title, $wrap_close );
    }
    
    /**
     * Outputs the selected content option if any
     *
     * @param array $instance The settings for the particular instance of the widget.
     */
    public static function do_post_content( $instance ) {
        //* Bail if empty show param
        if ( empty( $instance['show_content'] ) ) return;
        
        if ( $instance['show_content'] == 'excerpt' )
            the_excerpt();
        elseif ( $instance['show_content'] == 'content-limit' )
            the_content_limit( ( int ) $instance['content_limit'], esc_html( $instance['more_text'] ) );
        elseif ( $instance['show_content'] == 'content' )
            the_content( esc_html( $instance['more_text'] ) );
        else
            do_action( 'gs_featured_content_show_content' );
    }

    /**
     * Outputs post meta if option is selected and anything is in the post meta field
     *
     * @param array $instance The settings for the particular instance of the widget.
     */
    public static function do_post_meta( $instance ) {
        if ( ! empty( $instance['show_archive_line'] ) && ! empty( $instance['post_meta'] ) )
            printf( '<p class="post-meta">%s</p>', do_shortcode( esc_html( $instance['post_meta'] ) ) );
    }
    
    /**
     * Form submit script.
     */
    public static function form_submit() {
?>
<script type="text/javascript">
(function(jQ) {
    jQ('select.gs-widget-control-save').change( function() {
        var t=setTimeout(wpWidgets.save( jQ(this).closest('div.widget'), 0, 1, 0 ),2000);
        return false;
    });
})(jQuery);
</script>
<?php
        }
    
    /**
     * Inserts Author Gravatar if option is selected
     *
     * @param array $instance The settings for the particular instance of the widget.
     */
    public static function do_gravatar( $instance ) {
        if ( ! empty( $instance['show_gravatar'] ) ) {
            
            $tag = 'a';
            switch( $instance['link_gravatar'] ) {
                case 'archive' :
                    $before = 'href="'. get_author_posts_url( get_the_author_meta( 'ID' ) ) .'"';
                    break;
                
                case 'website' :
                    $before = 'href="'. get_the_author_meta( 'user_url' ) .'"';
                    break;
                
                default :
                    $before = '';
                    $tag = 'span';
                    break;
            }
            
            printf( '<%1$s %2$s class="%3$s">%4$s</%1$s>',
                $tag, 
                $before, 
                esc_attr( $instance['gravatar_alignment'] ), 
                get_avatar( get_the_author_meta( 'ID' ), 
                $instance['gravatar_size'] )
            );
            
        }
    }
    
    /**
     * The Posts Navigation/Pagination.
     * 
     * @param array $instance The settings for the particular instance of the widget.
     */
    public static function do_posts_nav( $instance ) {
        if ( ! empty( $instance['show_paged'] ) )
                genesis_posts_nav();
    }
    
    protected static function get_transient( $name ) {
        if ( 40 < strlen( $name ) )
            $name = substr( $string, 0, 40 );
        if ( is_multisite() )
            return get_site_transient( $name );
        else
            return get_transient( $name );
    }
    
    // @todo ensure transient name is < 40 chars
        // Enable multisite transients
    protected static function set_transient( $name, $value, $time = 86400 ) {
        if ( 40 < strlen( $name ) )
            $name = substr( $string, 0, 40 );
        if ( is_multisite() )
            set_site_transient( $name, $value, $time );
        else
            set_transient( $name, $value, $time );
    }
    
    // @todo ensure transient name is < 40 chars
        // Enable multisite transients
    protected static function delete_transient( $name ) {
        if ( 40 < strlen( $name ) )
            $name = substr( $string, 0, 40 );
        if ( is_multisite() )
            delete_site_transient( $name );
        else
            delete_transient( $name );
    }
    
    /**
     * The More Posts from Category.
     * 
     * @param array $instance The settings for the particular instance of the widget.
     */
    public static function do_more_from_category( $instance ) {
        $posts_term = $instance['posts_term'];
        $taxonomy   = $instance['taxonomy'];
        
        if ( ! empty( $instance['more_from_category'] ) && ! empty( $posts_term['0'] ) ) {
            GS_Featured_Content::action( 'gsfc_category_more', $instance );
            GS_Featured_Content::action( 'gsfc_taxonomy_more', $instance );
            GS_Featured_Content::action( 'gsfc_' . $taxonomy . '_more', $instance );
            $term = get_term_by( 'slug', $posts_term['1'], $taxonomy );
			printf(
				'<p class="more-from-%s"><a href="%1$s" title="%2$s">%3$s</a></p>',
                $taxonomy,
				esc_url( get_term_link( $posts_term['1'], $taxonomy ) ),
				esc_attr( $term->name ),
				esc_html( $instance['more_from_category_text'] )
			);
        }
        
        GS_Featured_Content::action( 'gsfc_after_category_more', $instance );
        GS_Featured_Content::action( 'gsfc_after_taxonomy_more', $instance );
        GS_Featured_Content::action( 'gsfc_after_' . $taxonomy . '_more', $instance );
    }
    
    /**
     * The EXTRA Posts (list).
     * 
     * @param array $instance The settings for the particular instance of the widget.
     */
    public static function do_extra_posts( $instance ) {
        global $wp_query;
        if ( empty( $instance['extra_posts'] ) && empty( $instance['extra_num'] ) ) return;
        
        $before_title = $instance['widget_args']['before_title'];
        
        if ( ! empty( $instance['extra_title'] ) )
            echo build_tag( $before_title ) . esc_html( $instance['extra_title'] ) . $after_title;;

        $offset = intval( $instance['posts_num'] ) + intval( $instance['posts_offset'] );
        
        $extra_posts_args = array_merge(
            $instance['q_args'],
            array(
                'showposts' => $instance['extra_num'],
                'offset'    => $offset,
                'post_type' => $instance['post_type'],
                'orderby'   => $instance['orderby'],
                'order'     => $instance['order'],
                'meta_key'  => $instance['meta_key'],
                'paged'     => $page
            )
        );
        
        $extra_posts_args = apply_filters( 'gsfc_extra_post_args', $extra_posts_args, $instance );
        
        if ( !empty( $instance['optimize'] ) && !empty( $instance['custom_field'] ) ) {
            if ( ! empty( $instance['delete_transients'] ) )
                GS_Featured_Content::delete_transient( 'gsfc_extra_posts_' . $instance['custom_field'] );
            if ( false === ( $gsfc_query = GS_Featured_Content::get_transient( 'gsfc_extra_posts_' . $instance['custom_field'] ) ) ) {
                $gsfc_query = new WP_Query( $extra_posts_args );
                $time = !empty( $instance['transients_time'] ) ? (int)$instance['transients_time'] : 60 * 60 * 24;
                GS_Featured_Content::set_transient( 'gsfc_extra_posts_' . $instance['custom_field'], $gsfc_query, $time );
            }
        } else {
            $gsfc_query = new WP_Query( $extra_posts_args );
        }
        
        $listitems = '';
        $items = array();
        
        if ( $gsfc_query->have_posts() ) :
            GS_Featured_Content::action( 'gsfc_before_list_items', $instance );
            while ( $gsfc_query->have_posts() ) : $gsfc_query->the_post();
                GS_Featured_Content::action( 'gsfc_before_list_items', $instance );
                
                $_genesis_displayed_ids[] = get_the_ID();
                $listitems .= sprintf( '<li><a href="%s" title="%s">%s</a></li>', get_permalink(), the_title_attribute( 'echo=0' ), get_the_title() );
                $items[] = get_post();
                
            endwhile;

            if ( strlen( $listitems ) > 0 && ( 'drop_down' != $instance['extra_format'] ) )
                echo apply_filters( 'gsfc_list_items', sprintf( '<ul>%s</ul>', $listitems ), $instance, $listitems, $items );
            elseif ( strlen( $listitems ) > 0 ) {
                printf(
                    '<select id="%s" value="%s"><option value="none">%s %s</option>%s</select>',
                    $this->get_field_id( 'extra_format' ),
                    get_permalink(), __( 'Select', 'gsfc' ),
                    $instance['post_type'],
                    $listitems
                );
            }
                
            GS_Featured_Content::action( 'gsfc_after_list_items', $instance );
            
        endif;

        //* Restore original query
        wp_reset_query();
    }
    
    /**
     * Used to exclude taxonomies and related terms from list of available terms/taxonomies in widget form()
     *
     * @param string $taxonomy 'taxonomy' being tested
     * @return string
     */
    public static function exclude_taxonomies( $taxonomy ) {
        $filters = array( '', 'nav_menu' );
        $filters = apply_filters( 'gsfc_exclude_taxonomies', $filters );
        return ( ! in_array( $taxonomy->name, $filters ) );
    }

    /**
     * Used to exclude post types from list of available post_types in widget form()
     *
     * @param string $type 'post_type' being tested
     * @return string
     */
    public static function exclude_post_types( $type ) {
        $filters = array( '', 'attachment' );
        $filters = apply_filters( 'gsfc_exclude_post_types', $filters );
        return( !in_array( $type, $filters ) );
    }
    
    /**
     * Filters the Post Limit to allow pagination with offset
     *
     * @global int $paged
     * @global string $myOffset 'integer'
     * @param string $limit
     * @return string
     */
    public static function post_limit( $limit ) {
        global $paged, $myOffset;
        if ( empty( $paged ) ) {
            $paged = 1;
        }
        $postperpage = intval( get_option( 'posts_per_page' ) );
        $pgstrt = ((intval( $paged ) - 1) * $postperpage) + $myOffset . ', ';
        $limit = 'LIMIT ' . $pgstrt . $postperpage;
        return $limit;
    }

    /**
     * Get image size options.
     * 
     * @return array Array of image size options.
     */    
    public static function get_image_size_options() {
        $sizes = genesis_get_additional_image_sizes();
        $image_size_opt['thumbnail'] = 'thumbnail ('. get_option( 'thumbnail_size_w' ) . 'x' . get_option( 'thumbnail_size_h' ) . ')';
		foreach( ( array )$sizes as $name => $size ) 
			$image_size_opt[ $name ] = esc_html( $name ) . ' (' . $size['width'] . 'x' . $size['height'] . ')';
        return $image_size_opt;
    }
    
    /**
     * Returns form fields in boxes in columns
     * 
     * @return array $columns Array of form fields.
     */
    protected static function get_form_fields() {
        $columns = array(
			'col1' => array(
                //* Box 1
				array(
					'post_type'               => array(
						'label'       => __( 'Post Type', 'gsfc' ),
						'description' => '',
						'type'        => 'post_type_select',
						'save'        => true,
						'requires'    => '',
					),
					'page_id'                 => array(
						'label'       => __( 'Page', 'gsfc' ),
						'description' => '',
						'type'        => 'page_select',
						'save'        => true,
						'requires'    => array(
							'post_type',
							'page',
							false
						),
					),
					'posts_term'              => array(
						'label'       => __( 'Taxonomy and Terms', 'gsfc' ),
						'description' => '',
						'type'        => 'select_taxonomy',
						'save'        => false,
						'requires'    => array(
							'post_type',
							'page',
							true
						),
					),
					'exclude_terms'           => array(
						'label'       => sprintf( __( 'Exclude Terms by ID %s (comma separated list)', 'gsfc' ), '<br />' ),
						'description' => '',
						'type'        => 'text',
						'save'        => false,
						'requires'    => array(
							'post_type',
							'page',
							true
						),
					),
					'include_exclude'         => array(
						'label'       => '',
						'description' => '',
						'type'        => 'select',
						'options'     => array(
							''        => __( 'Select'  , 'gsfc' ),
							'include' => __( 'Include' , 'gsfc' ),
							'exclude' => __( 'Exclude' , 'gsfc' ),
						),
						'save'        => true,
						'requires'    => array(
							'page_id',
							'',
							false
						),
					),
					'post_id'                 => array(
						'label'       => $instance['post_type'] . ' ' . __( 'ID', 'gsfc' ),
						'description' => '',
						'type'        => 'text',
						'save'        => false,
						'requires'    => array(
							'include_exclude',
							'',
							true
						),
					),
					'posts_num'               => array(
						'label'       => __( 'Number of Posts to Show', 'gsfc' ),
						'description' => '',
						'type'        => 'text_small',
						'save'        => false,
						'requires'    => array(
							'page_id',
							'',
							false
						),
					),
					'posts_offset'            => array(
						'label'       => __( 'Number of Posts to Offset', 'gsfc' ),
						'description' => '',
						'type'        => 'text_small',
						'save'        => false,
						'requires'    => array(
							'page_id',
							'',
							false
						),
					),
					'orderby'                 => array(
						'label'       => __( 'Order By', 'gsfc' ),
						'description' => '',
						'type'        => 'select',
						'options'     => array(
							'date'           => __( 'Date'              , 'gsfc' ),
							'title'          => __( 'Title'             , 'gsfc' ),
							'parent'         => __( 'Parent'            , 'gsfc' ),
							'ID'             => __( 'ID'                , 'gsfc' ),
							'comment_count'  => __( 'Comment Count'     , 'gsfc' ),
							'rand'           => __( 'Random'            , 'gsfc' ),
							'meta_value'     => __( 'Meta Value'        , 'gsfc' ),
							'meta_value_num' => __( 'Numeric Meta Value', 'gsfc' ),
						),
						'save'        => false,
						'requires'    => array(
							'page_id',
							'',
							false
						),
					),
					'order'                   => array(
						'label'       => __( 'Sort Order', 'gsfc' ),
						'description' => '',
						'type'        => 'select',
						'options'     => array(
							'DESC'    => __( 'Descending (3, 2, 1)', 'gsfc' ),
							'ASC'     => __( 'Ascending (1, 2, 3)' , 'gsfc' ),
						),
						'save'        => false,
						'requires'    => array(
							'page_id',
							'',
							false
						),
					),
					'meta_key'               => array(
						'label'       => __( 'Meta Key', 'gsfc' ),
						'description' => '',
						'type'        => 'text',
						'save'        => false,
						'requires'    => array(
							'page_id',
							'',
							false
						),
					),
					'paged'                   => array(
						'label'       => __( 'Work with Pagination', 'gsfc' ),
						'description' => '',
						'type'        => 'checkbox',
						'save'        => false,
						'requires'    => array(
							'post_type',
							'page',
							true
						),
					),
					'show_paged'              => array(
						'label'       => __( 'Show Page Navigation', 'gsfc' ),
						'description' => '',
						'type'        => 'checkbox',
						'save'        => false,
						'requires'    => array(
							'post_type',
							'page',
							true
						),
					),
				),
                
                //* Box 2
				array(
					'show_gravatar'           => array(
						'label'       => __( 'Show Author Gravatar', 'gsfc' ),
						'description' => '',
						'type'        => 'checkbox',
						'save'        => true,
						'requires'    => '',
					),
					'gravatar_size'          => array(
						'label'       => __( 'Gravatar Size', 'gsfc' ),
						'description' => '',
						'type'        => 'select',
						'options'     => array(
							'45'      => __( 'Small (45px)'       , 'gsfc' ),
							'65'      => __( 'Medium (65px)'      , 'gsfc' ),
							'85'      => __( 'Large (85px)'       , 'gsfc' ),
							'125'     => __( 'Extra Large (125px)', 'gsfc' ),
						),
						'save'        => false,
						'requires'    => array(
							'show_gravatar',
							'',
							true
						),
					),
					'link_gravatar'          => array(
						'label'       => '',
						'description' => '',
						'type'        => 'select',
						'options'     => array(
							''            => __( 'Do not link gravatar'  , 'gsfc' ),
							'archive'     => __( 'Link to author archive', 'gsfc' ),
							'website'     => __( 'Link to author website', 'gsfc' ),
						),
						'save'        => false,
						'requires'    => array(
							'show_gravatar',
							'',
							true
						),
					),
					'gravatar_alignment'      => array(
						'label'       => __( 'Gravatar Alignment', 'gsfc' ),
						'description' => '',
						'type'        => 'select',
						'options'     => array(
							''           => __( 'None' , 'gsfc' ),
							'alignleft'  => __( 'Left' , 'gsfc' ),
							'alignright' => __( 'Right', 'gsfc' ),
						),
						'save'        => false,
						'requires'    => array(
							'show_gravatar',
							'',
							true
						),
					),
				),
                //* Box 3
				array(
					'class'                  => array(
						'label'       => __( 'Class', 'gsfc' ),
						'description' => __( 'Fill in this field if you want to add a custom post class.', 'gsfc' ),
						'type'        => 'text',
						'save'        => false,
						'requires'    => '',
					),
                    'add_column_classes'     => array(
						'label'       => __( 'Need to add column classes?', 'gsfc' ),
						'description' => 'Check to add column classes to your site (supports fifths).',
						'type'        => 'checkbox',
						'save'        => true,
						'requires'    => '',
					),
                    'column_class'           => array(
						'label'       => __( 'Column Class', 'gsfc' ),
						'description' => __( 'Fill in this field if you want to add a custom post class. Will automagically add <code>first</code> where appropriate.', 'gsfc' ),
						'type'        => 'select',
						'options'     => array(
							''              => __( 'Select Class', 'gsfc' ),
							'one-half'      => __( 'One Half', 'gsfc' ),
							'one-third'     => __( 'One Third', 'gsfc' ),
							'one-fourth'    => __( 'One Fourth', 'gsfc' ),
							'one-fifth'     => __( 'One Fifth', 'gsfc' ),
							'one-sixith'    => __( 'One Sixth', 'gsfc' ),
							'two-thirds'    => __( 'Two Thirds', 'gsfc' ),
							'three-fourths' => __( 'Three Fourths', 'gsfc' ),
							'two-fifths'    => __( 'Two Fifths', 'gsfc' ),
							'three-fifths'  => __( 'Three Fifths', 'gsfc' ),
							'four-fifths'   => __( 'Four Fifths', 'gsfc' ),
							'five-sixths'   => __( 'Five Sixths', 'gsfc' ),
						),
						'save'        => false,
						'requires'    => '',
					),
				),
			),
			'col2' => array(
                //* Box 1
				array(
					'show_image'              => array(
						'label'       => __( 'Show Featured Image', 'gsfc' ),
						'description' => '',
						'type'        => 'checkbox',
						'save'        => true,
						'requires'    => '',
					),
					'link_image'              => array(
						'label'       => '',
						'description' => '',
						'type'        => 'select',
						'options'     => array(
							'1' => __( 'Link Image to Post', 'gsfc' ),
							'2' => __( 'Don\'t Link Image' , 'gsfc' ),
						),
						'save'        => true,
						'requires'    => array(
							'show_image',
							'',
							true
						),
					),
					'link_image_field'              => array(
						'label'       => __( 'Link ( Defaults to Permalink )'),
						'description' => '',
						'type'        => 'text',
						'save'        => false,
						'requires'    => array(
							'link_image',
							'1',
							false
						),
					),
					'image_size'              => array(
						'label'       => '',
						'description' => '',
						'type'        => 'select',
						'options'     => GS_Featured_Content::get_image_size_options(),
						'save'        => false,
						'requires'    => array(
							'show_image',
							'',
							true
						),
					),
					'image_position'          => array(
						'label'       => __( 'Image Placement', 'gsfc' ),
						'description' => '',
						'type'        => 'select',
						'options'     => array(
							'before-title'  => __( 'Before Title' , 'gsfc' ),
							'after-title'   => __( 'After Title'  , 'gsfc' ),
							'after-content' => __( 'After Content', 'gsfc' ),
						),
						'save'        => false,
						'requires'    => array(
							'show_image',
							'',
							true
						),
					),
					'image_alignment'         => array(
						'label'       => '',
						'description' => '',
						'type'        => 'select',
						'options'     => array(
							''            => __( 'None'  , 'gsfc' ),
							'alignleft'   => __( 'Left'  , 'gsfc' ),
							'alignright'  => __( 'Right' , 'gsfc' ),
							'aligncenter' => __( 'Center', 'gsfc' ),
						),
						'save'        => false,
						'requires'    => array(
							'show_image',
							'',
							true
						),
					),
				),
                //* Box 2
				array(
					'show_title'              => array(
						'label'       => __( 'Show Post Title', 'gsfc' ),
						'description' => '',
						'type'        => 'checkbox',
						'save'        => true,
						'requires'    => '',
					),
					'title_limit'             => array(
						'label'       => __( 'Limit title to', 'gsfc' ),
						'description' => __( ' characters', 'gsfc' ),
						'type'        => 'text_small',
						'save'        => false,
						'requires'    => array(
							'show_title',
							'',
							true
						),
					),
					'title_cutoff'             => array(
						'label'       => __( 'Title Cutoff Symbol', 'gsfc' ),
						'description' => '',
						'type'        => 'text_small',
						'save'        => false,
						'requires'    => array(
							'show_title',
							'',
							true
						),
					),
					'link_title'              => array(
						'label'       => '',
						'description' => '',
						'type'        => 'select',
						'options'     => array(
							'1' => __( 'Link Title to Post', 'gsfc' ),
							'2' => __( 'Don\'t Link Title' , 'gsfc' ),
						),
						'save'        => true,
						'requires'    => array(
							'show_title',
							'',
							true
						),
					),
					'link_title_field'              => array(
						'label'       => __( 'Link ( Defaults to Permalink )', 'gsfc' ),
						'description' => '',
						'type'        => 'text',
						'save'        => false,
						'requires'    => array(
							'link_title',
							'1',
							false
						),
					),
					'show_byline'             => array(
						'label'       => __( 'Show Post Info', 'gsfc' ),
						'description' => '',
						'type'        => 'checkbox',
						'save'        => true,
						'requires'    => '',
					),
					'post_info'               => array(
						'label'       => __( 'Post Info', 'gsfc' ),
						'description' => '',
						'type'        => 'text',
						'save'        => false,
						'requires'    => array(
							'show_byline',
							'',
							true
						),
					),
					'show_content'            => array(
						'label'       => __( 'Content Type', 'gsfc' ),
						'description' => '',
						'type'        => 'select',
						'options'     => array(
							'content'       => __( 'Show Content'      , 'gsfc' ),
							'excerpt'       => __( 'Show Excerpt'      , 'gsfc' ),
							'content-limit' => __( 'Show Content Limit', 'gsfc' ),
							''              => __( 'No Content'        , 'gsfc' ),
						),
						'save'        => true,
						'requires'    => '',
					),
					'content_limit'           => array(
						'label'       => __( 'Limit content to', 'gsfc' ),
						'description' => __( ' characters', 'gsfc' ),
						'type'        => 'text_small',
						'save'        => false,
						'requires'    => array(
							'show_content',
							'content-limit',
							false
						),
					),
                    'excerpt_limit'             => array(
						'label'       => __( 'Limit excerpt to', 'gsfc' ),
						'description' => __( ' words', 'gsfc' ),
						'type'        => 'text_small',
						'save'        => false,
						'requires'    => array(
							'show_content',
							'excerpt',
							false
						),
					),
					'excerpt_cutoff'             => array(
						'label'       => __( 'Title Cutoff Symbol', 'gsfc' ),
						'description' => '',
						'type'        => 'text_small',
						'save'        => false,
						'requires'    => array(
							'show_content',
							'excerpt',
							false
						),
					),
					'show_archive_line'       => array(
						'label'       => __( 'Show Post Meta', 'gsfc' ),
						'description' => '',
						'type'        => 'checkbox',
						'save'        => true,
						'requires'    => array(
							'post_type',
							'page',
							true
						),
					),

					'post_meta'               => array(
						'label'       => '',
						'description' => '',
						'type'        => 'text',
						'save'        => false,
						'requires'    => array(
							'show_archive_line',
							'',
							true
						),
					),
					'more_text'               => array(
						'label'       => __( 'More Text (if applicable)', 'gsfc' ),
						'description' => '',
						'type'        => 'text',
						'save'        => false,
						'requires'    => '',
					),
				),
                //* Box 3
				array(
					'extra_posts'             => array(
						'label'       => __( 'Display List of Additional Posts', 'gsfc' ),
						'description' => '',
						'type'        => 'checkbox',
						'save'        => true,
						'requires'    => array(
							'post_type',
							'page',
							true
						),
					),
					'extra_title'             => array(
						'label'       => __( 'Title', 'gsfc' ),
						'description' => '',
						'type'        => 'text',
						'save'        => false,
						'requires'    => array(
							'extra_posts',
							'',
							true
						),
					),
					'extra_num'               => array(
						'label'       => __( 'Number of Posts to Show', 'gsfc' ),
						'description' => '',
						'type'        => 'text_small',
						'save'        => false,
						'requires'    => array(
							'extra_posts',
							'',
							true
						),
					),
					'extra_format'            => array(
						'label'       => __( 'Extra Post Format', 'gsfc' ),
						'description' => '',
						'type'        => 'select',
						'options'     => array(
							'ul'        => __( 'Unordered List', 'gsfc' ),
							'ol'        => __( 'Ordered List'  , 'gsfc' ),
							'drop_down' => __( 'Drop Down'     , 'gsfc' ),
						),
						'save'        => false,
						'requires'    => array(
							'extra_posts',
							'',
							true
						),
					),
				),
                //* Box 4
				array(
					'more_from_category'      => array(
						'label'       => __( 'Show Category Archive Link', 'gsfc' ),
						'description' => '',
						'type'        => 'checkbox',
						'save'        => true,
						'requires'    => array(
							'post_type',
							'page',
							true
						),
					),
					'more_from_category_text' => array(
						'label'       => __( 'Link Text', 'gsfc' ),
						'description' => '',
						'type'        => 'text',
						'save'        => false,
						'requires'    => array(
							'more_from_category',
							'',
							true
						),
					),
					'archive_link'            => array(
						'label'       => __( 'Fill in this value with a URL if you wish to display an archive link when showing all terms or to override the normal archive link to another URL', 'gsfc' ),
						'description' => '',
						'type'        => 'text',
						'save'        => false,
						'requires'    => array(
							'more_from_category',
							'',
							true
						),
					),
				),
                //* Box 5
				array(
					'optimize'               => array(
						'label'       => __( 'Optimize?', 'gsfc' ),
						'description' => 'Check to optimize WP_Query & enable site transients for the query results. You MUST set custom field.',
						'type'        => 'checkbox',
						'save'        => true,
						'requires'    => '',
					),
                    'delete_transients'      => array(
						'label'       => __( 'Delete Transients?', 'gsfc' ),
						'description' => '',
						'type'        => 'checkbox',
						'save'        => true,
						'requires'    => '',
					),
                    'transients_time'         => array(
						'label'       => __( 'Set Transients Expiration (seconds)', 'gsfc' ),
						'description' => '',
						'type'        => 'text',
						'save'        => false,
						'requires'    => '',
					),
                    'custom_field'            => array(
						'label'       => __( 'Instance Identification Field', 'gsfc' ),
						'description' => __( 'Fill in this field if you need to test against an $instance value not included in the form', 'gsfc' ),
						'type'        => 'text',
						'save'        => false,
						'requires'    => '',
					),
				),
			),
		);
        return apply_filters( 'gsfc_form_fields', $columns, $instance );
    }
    
    /**
     * Adds a class to tag, checks whether any classes currently exist.
     * 
     * @param string $old_tag Old tag
     * 
     * @return string HTML opening tag.
     */
    public static function build_tag( $old_tag ) {
        
        preg_match_all( '/(\S+)=["\']?((?:.(?!["\']?\s+(?:\S+)=|[>"\']))+.)["\']?/si', $old_tag, $result, PREG_PATTERN_ORDER );
        if ( !in_array( 'class', $result[1] ) ) {
            $tag = str_replace( '>', ' class="additional-posts-title">', $old_tag ) . esc_html( $instance['extra_title'] ) . $after_title;
        } else {
            $tag = '<';
            preg_match_all( '/<([a-zA-Z0-9]*)[^>]*>/si', $old_tag, $r, PREG_PATTERN_ORDER );
            $tag .= $r[1][0];
            
            foreach( array_combine( $result[1], $result[2] ) as $attr => $value ) {
                if ( 'class' == $attr )
                    $tag .= sprintf( ' %s="%s"', $attr, $value . ' additional-posts-title' );
                else
                    $tag .= sprintf( ' %s="%s"', $attr, $value );
            }
            
            $tag .= '>';
        }
        return $tag;
    }

    /**
     * Generate random character string (defaults to 10 chars)
     * 
     * @param int $length String length. 
     * 
     * @return string Randomized string.
     */
    protected static function generate_random_string( $length = 10 ) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $random_string = '';
        for ( $i = 0; $i < $length; $i++ ) {
            $random_string .= $characters[ rand( 0, strlen( $characters ) - 1 ) ];
        }
        return $random_string;
    }
    
	/**
	 * Echo the settings update form.
	 *
	 * @since 0.1.8
	 *
	 * @param array $instance Current settings
	 */
	public function form( $instance ) {
        GS_Featured_Content::$widget_instance &= $instance;
        
		//* Merge with defaults
		$instance = wp_parse_args( (array) $instance, $this->defaults );
        
        //* Get Columns
        $columns = GS_Featured_Content::get_form_fields();
        
        //* Title Field
        echo '<p><label for="'. $this->get_field_id( 'title' ) .'">'. __( 'Title', 'gsfc' ) .':</label><input type="text" id="'. $this->get_field_id( 'title' ) .'" name="'. $this->get_field_name( 'title' ) .'" value="'. esc_attr( $instance['title'] ) .'" style="width:99%;" /></p>';
        
        foreach( $columns as $column => $boxes ) {
			if( 'col1' == $column )
				echo '<div style="float: left; width: 250px;">';
			else 
				echo '<div style="float: right; width: 250px;">';
			
			foreach( $boxes as $box ){
				echo '<div style="background: #f1f1f1; border: 1px solid #DDD; padding: 10px 10px 0px 10px; margin-bottom: 5px;">';
				
				foreach( $box as $field_id => $args ){
					$class = $args['save']     ? 'class="gs-widget-control-save" ' : '';
					$style = $args['requires'] ? ' style="'. GS_Featured_Content::get_display_option( $instance, $args['requires'][0], $args['requires'][1], $args['requires'][2] ) .'"' : '';
					
					switch( $args['type'] ) {
						case 'post_type_select' :
							echo '<p><label for="'. $this->get_field_id( $field_id ) .'">'. $args['label'] .':</label>
								<select '. $class .'id="'. $this->get_field_id( $field_id ) .'" name="'. $this->get_field_name( $field_id ) .'">';
							
							$args = array(
								'public' => true
							);
							$post_types = get_post_types( $args, 'names', 'and' );
							$post_types = array_filter( $post_types, array( __CLASS__, 'exclude_post_types' ) );

							foreach ( $post_types as $post_type ) 
								echo '<option style="padding-right:10px;" value="'. esc_attr( $post_type ) .'" '. selected( esc_attr( $post_type ), $instance['post_type'], false ) .'>'. esc_attr( $post_type ) .'</option>'; 

								echo '<option style="padding-right:10px;" value="any" '. selected( 'any', $instance['post_type'], false ) .'>'. __( 'any', 'gsfc' ) .'</option>'; 
								
							echo '</select></p>';
							break;

                        case 'page_select' :
							echo '<p'. $style .'><label for="'. $this->get_field_id( $field_id ) .'">'. $args['label'] .':</label><select '. $class .' id="'. $this->get_field_id( $field_id ) .'" name="'. $this->get_field_name( $field_id ) .'">
									<option value="" '. selected( '', $instance['page_id'], false ) .'>'. attribute_escape( __( 'Select page', 'gsfc' ) ) .'</option>';

									$pages = get_pages();
									foreach ( $pages as $page ) 
										echo '<option style="padding-right:10px;" value="'. esc_attr( $page->ID ) .'" '. selected( esc_attr( $page->ID ), $instance['page_id'], false ) .'>'. esc_attr( $page->post_title ) .'</option>';
							echo '</select>
							</p>';
							break;
						
						case 'select_taxonomy' :
							echo '<p'. $style .'"><label for="'. $this->get_field_id( $field_id ) .'">'. $args['label'] .':</label><select style="max-width: 228px;" id="'. $this->get_field_id( $field_id ) .'" name="'. $this->get_field_name( $field_id ) .'">
									<option style="padding-right:10px;" value="" '. selected( '', $instance['posts_term'], false ) .'>'. __( 'All Taxonomies and Terms', 'gsfc' ) .'</option>';
									
									$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
									$taxonomies = array_filter( $taxonomies, array( __CLASS__, 'exclude_taxonomies' ) );

									foreach ( $taxonomies as $taxonomy ) {
										$query_label = '';
										if ( !empty( $taxonomy->query_var ) )
											$query_label = $taxonomy->query_var;
										else
											$query_label = $taxonomy->name;
										
										echo '<optgroup label="'. esc_attr( $taxonomy->labels->name ) .'">
											<option style="margin-left: 5px; padding-right:10px;" value="'. esc_attr( $query_label ) .'" '. selected( esc_attr( $query_label ), $instance['posts_term'], false ) .'>'. $taxonomy->labels->all_items .'</option>';
										
										$terms = get_terms( $taxonomy->name, 'orderby=name&hide_empty=1' );
										
										foreach ( $terms as $term )
											echo '<option style="margin-left: 8px; padding-right:10px;" value="'. esc_attr( $query_label ) . ',' . $term->slug .'" '. selected( esc_attr( $query_label ) . ',' . $term->slug, $instance['posts_term'], false ) .'>-' . esc_attr( $term->name ) .'</option>';
											
                                        echo '</optgroup>'; 
									}
								echo '</select></p>';
							break;
							
						case 'text' :
							echo $args['description'] ? '<p>'. $args['description'] .'</p>' : '';

							echo '<p'. $style .'><label for="'. $this->get_field_id( $field_id ) .'">'. $args['label'] .':</label>
									<input type="text" id="'. $this->get_field_id( $field_id ) .'" name="'. $this->get_field_name( $field_id ) .'" value="'. esc_attr( $instance[$field_id] ) .'" style="width:95%;" /></p>';
							break;
						
						case 'text_small' :
							echo '<p'. $style .'><label for="'. $this->get_field_id( $field_id ) .'">'. $args['label'] .':</label>
									<input type="text" id="'. $this->get_field_id( $field_id ) .'" name="'. $this->get_field_name( $field_id ) .'" value="'. esc_attr( $instance[$field_id] ) .'" size="2" />'. $args['description'] .'</p>';
							break;
							
						case 'select' :
							echo '<p'. $style .'"><label for="'. $this->get_field_id( $field_id ) .'">'. $args['label'] .' </label>
								<select '. $class .'id="'. $this->get_field_id( $field_id ) .'" name="'. $this->get_field_name( $field_id ) .'">';
							
								foreach( $args['options'] as $value => $label )
									echo '<option style="padding-right:10px;" value="'. $value .'" '. selected( $value, $instance[$field_id], false ) .'>'. $label .'</option>';
								
								echo '</select></p>';
							break;
							
						case 'checkbox' :
							echo '<p'. $style .'><input '. $class .'id="'. $this->get_field_id( $field_id ).'" type="checkbox" name="'. $this->get_field_name( $field_id ) .'" value="1" '. checked( 1, $instance[$field_id], false ) .'/> <label for="'. $this->get_field_id( $field_id ) .'">'. $args['label'] .'</label></p>';
							break;
					}
				}
				
				echo '</div>';
			}
			
			echo '</div>';
				
		}
        
    }
    
    /**
     * Returns "display: none;" if option and value match, or of they don't match with $standard is set to false
     *
     * @param array $instance Values set in widget isntance.
     * @param mixed $option Instance option to test.
     * @param mixed $value Value to test against.
     * @param boolean $standard Echo standard return false for oposite.
     */
    protected static function get_display_option( $instance, $option='', $value='', $standard=true ) {
        $display = '';
        if ( is_array( $option ) ) {
            foreach ( $option as $key ) {
                if ( in_array( $instance[$key], $value ) )
                    $display = 'display: none;';
            }
        }
        elseif ( is_array( $value ) ) {
            if ( in_array( $instance[$option], $value ) )
                $display = 'display: none;';
        }
        else {
            if ( $instance[$option] == $value )
                $display = 'display: none;';
        }
        if ( $standard == false ) {
            if ( $display == 'display: none;' )
                $display = '';
            else
                $display = 'display: none;';
        }
        return $display;
    }
    
	/**
	 * Echo the widget content.
	 *
	 * @since 0.1.8
	 *
	 * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function widget( $args, $instance ) {
        GS_Featured_Content::$widget_instance = $instance;
		global $wp_query, $_genesis_displayed_ids, $gs_counter;

		extract( $args );
        $instance['widget_args'] = $args;

		//* Merge with defaults
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		echo $before_widget;
        add_filter( 'post_class', array( 'GS_Featured_Content', 'post_class' ) );
        
        if ( ! empty( $instance['posts_offset'] ) && ! empty( $instance['paged'] ) )
            add_filter( 'post_limits', array( 'GS_Featured_Content', 'post_limit' ) );
        else
            remove_filter( 'post_limits', array( 'GS_Featured_Content', 'post_limit' ) );

		//* Set up the author bio
		if ( ! empty( $instance['title'] ) ) {
			echo $before_title . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $after_title;
        }
        
        $q_args = array();
        
        //* Page ID
        if ( ! empty( $instance['page_id'] ) )
            $q_args['page_id'] = $instance['page_id'];
        
        //* Term Args
        if ( ! empty( $instance['posts_term'] ) ) {
            $posts_term = explode( ',', $instance['posts_term'] );
            if ( $posts_term['0'] == 'category' )
                $posts_term['0'] = 'category_name';
            if ( $posts_term['0'] == 'post_tag' )
                $posts_term['0'] = 'tag';
            if ( isset( $posts_term['1'] ) )
                $q_args[$posts_term['0']] = $posts_term['1'];
        }
        
        if ( ! empty( $posts_term['0'] ) ) {
            if ( $posts_term['0'] == 'category_name' )
                $taxonomy = 'category';
            elseif ( $posts_term['0'] == 'tag' )
                $taxonomy = 'post_tag';
            else
                $taxonomy = $posts_term['0'];
        } else {
            $taxonomy = 'category';
        }
        $instance['posts_term'] = $posts_term;
        $instance['taxonomy']   = $taxonomy;
        GS_Featured_Content::$widget_instance = $instance;
        if ( ! empty( $instance['exclude_terms'] ) ) {
            $exclude_terms = explode( ',', str_replace( ' ', '', $instance['exclude_terms'] ) );
            $q_args[$taxonomy . '__not_in'] = $exclude_terms;
        }
        
        //* Paged arg
        $page = '';
        if ( ! empty( $instance['paged'] ) )
            $page = get_query_var( 'paged' );
        
        
        //* Offset
        if ( ! empty( $instance['posts_offset'] ) ) {
            global $gs_offset;
            $gs_offset = $instance['posts_offset'];
            $q_args['offset'] = $gs_offset;
        }
        
        //* Post IDs
        if ( ! empty( $instance['post_id'] ) ) {
            $IDs = explode( ',', str_replace( ' ', '', $instance['post_id'] ) );
            if ( $instance['include_exclude'] == 'include' )
                $q_args['post__in'] = $IDs;
            else
                $q_args['post__not_in'] = $IDs;
        }
        
        //* Before Loop Action
        GS_Featured_Content::action( 'gs_before_loop', $instance );
        
        if ( 0 === $instance['posts_num'] ) return;
        
        //* Optimize Query
        if ( ! empty( $instance['optimize'] ) ) {
            $q_args['cache_results'] = false;
            if ( empty( $instance['paged'] ) && empty( $instance['show_paged']  ) )
                $q_args['no_found_rows'] = true;
        }
        
        $instance['q_args'] = $q_args;
        GS_Featured_Content::$widget_instance = $instance;
        $query_args = array_merge(
            $q_args,
            array(
                'post_type'      => $instance['post_type'], 
                'posts_per_page' => $instance['posts_num'], 
                'orderby'        => $instance['orderby'], 
                'order'          => $instance['order'], 
                'meta_key'       => $instance['meta_key'], 
                'paged'          => $page ,
            ) 
        );
        $instance['query_args'] = $query_args;
        GS_Featured_Content::$widget_instance = $instance;
        
        //* Exclude displayed IDs from this loop?
		if ( $instance['exclude_displayed'] )
			$query_args['post__not_in'] = (array) $_genesis_displayed_ids;
        
        $query_args = apply_filters( 'gsfc_query_args', $query_args, $instance );
        
        // get transient
		if ( !empty( $instance['optimize'] ) && !empty( $instance['custom_field'] ) ) {
            if ( ! empty( $instance['delete_transients'] ) )
                GS_Featured_Content::delete_transient( 'gsfc_extra_posts_' . $instance['custom_field'] );
            
            // Get transient, set transient if transient does not exist
            if ( false === ( $gsfc_query = GS_Featured_Content::get_transient( 'gsfc_main_' . $instance['custom_field'] ) ) ) {
                $gsfc_query = new WP_Query( $query_args );
                $time = !empty( $instance['transients_time'] ) ? $instance['transients_time'] : 60 * 60 * 24;
                GS_Featured_Content::set_transient( 'gsfc_main_' . $instance['custom_field'], $gsfc_query, $time );
            }
        } else {
            $gsfc_query = new WP_Query( $query_args );
        }

		if ( $gsfc_query->have_posts() ) : 
            while ( $gsfc_query->have_posts() ) : $gsfc_query->the_post();

                $_genesis_displayed_ids[] = get_the_ID();

                GS_Featured_Content::framework( $instance );

            endwhile; 
        
            GS_Featured_Content::action( 'gsfc_endwhile', $instance );
            
        endif;
        
        $gs_counter = 0;

        GS_Featured_Content::action( 'gsfc_after_loop', $instance );

		//* Restore original query
		wp_reset_query();
        
        GS_Featured_Content::action( 'gsfc_after_loop_reset', $instance );

		echo $after_widget;
        remove_filter( 'post_class', array( 'GS_Featured_Content', 'post_class' ) );
        remove_filter( 'post_limits', array( 'GS_Featured_Content', 'post_limit' ) );

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
		$new_instance['excerpt_cutoff'] = '...' == $new_instance['excerpt_cutoff'] ? '&hellip;' : $new_instance['excerpt_cutoff'];
		$new_instance['title_cutoff']   = '...' == $new_instance['title_cutoff'] ? '&hellip;' : $new_instance['title_cutoff'];
		$new_instance['title']          = strip_tags( $new_instance['title'] );
		$new_instance['more_text']      = strip_tags( $new_instance['more_text'] );
		$new_instance['post_info']      = wp_kses_post( $new_instance['post_info'] );
		$new_instance['custom_field']   = sanitize_title_with_dashes( $new_instance['custom_field'] );
        
        if ( false !== ( $gsfc_query = GS_Featured_Content::get_transient( 'gsfc_main_' . $instance['custom_field'] ) ) )
            GS_Featured_Content::delete_transient( 'gsfc_extra_posts_' . $instance['custom_field'] );
        
		return $new_instance;

	}
    
    
}
