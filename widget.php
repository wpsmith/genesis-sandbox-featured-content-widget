<?php

/**
 * Genesis Sandbox Featured Post Widget Classes
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

/**
 * Genesis Sandbox Featured Post widget class.
 *
 * @since 0.1.8
 *
 * @category   Genesis_Sandbox
 * @package    Widgets
 */
if ( ! class_exists( 'GS_Featured_Content' ) ) {
class GS_Featured_Content extends WP_Widget {
    
    /**
     * Holds a copy of the object for easy reference.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public static $widget_instance = array();
    public static $base = 'featured-content';
    public static $self;
    
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
        GS_Featured_Content::$self = $this;
        
        $gfwa = genesis_get_option( 'gsfc_gfwa' );
        if ( $gfwa )
            GS_Featured_Content::$base = 'featured-post';
        
		$this->defaults = apply_filters(
            'gsfc_defaults',
            array(
                'add_column_classes'      => 0,
                'archive_link'            => '',
                'byline_position'         => 'after-title',
                'class'                   => '',
                'column_classes'          => '',
                'content_limit'           => '',
                'count'                   => 0,
                'custom_field'            => '',
                'delete_transients'       => 0,
                'excerpt_cutoff'          => '&hellip;',
                'excerpt_limit'           => 55,
                'exclude_cat'             => '',
                'exclude_displayed'       => 0,
                'exclude_terms'           => '',
                'extra_format'            => 'ul',
                'extra_num'               => 3,
                'extra_posts'             => '',
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
                'optimize'                => 0,
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
                'show_content'            => '',
                'show_gravatar'           => 0,
                'show_image'              => 0,
                'show_paged'              => '',
                'show_sticky'             => '',
                'show_title'              => 0,
                'title'                   => '',
                'title_cutoff'            => '&hellip;',
                'title_limit'             => '',
                'transients_time'         => 86400,
                'widget_title_link'       => 0,
                'widget_title_link_href'  => '',
            )
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
        do_action( 'gsfc_actions', $this );
	}
    
    /**
     * Adds all Widget's Actions at once for easy removal.
     */
    public static function add() {
        $self = GS_Featured_Content::$self;
        
        //* Form Fields
        add_action( 'gsfc_output_form_fields', array( 'GS_Featured_Content', 'do_form_fields' ), 10, 2 );
        
        //* Post Class
        add_filter( 'post_class', array( 'GS_Featured_Content', 'post_class' ) );
        
        //* Excerpts
        add_filter( 'excerpt_length', array( 'GS_Featured_Content', 'excerpt_length' ) );
        add_filter( 'excerpt_more', array( 'GS_Featured_Content', 'excerpt_more' ) );
        
        //* Do Post Image
        add_filter( 'genesis_attr_gsfc-entry-image-widget', array( 'GS_Featured_Content', 'attributes_gsfc_entry_image_widget' ) );
        add_action( 'gsfc_before_post_content', array( 'GS_Featured_Content', 'do_post_image' ) );
        add_action( 'gsfc_post_content', array( 'GS_Featured_Content', 'do_post_image' ) );
        add_action( 'gsfc_after_post_content', array( 'GS_Featured_Content', 'do_post_image' ) );
        
        //* Do before widget post content
        add_action( 'gsfc_before_post_content', array( 'GS_Featured_Content', 'do_gravatar' ) );
        add_action( 'gsfc_before_post_content', array( 'GS_Featured_Content', 'do_post_title' ) );
        
        //* Maybe Linkify Widget Title
        add_action( 'gsfc_widget_title', array( $self, 'widget_title' ), 999, 3 );
        
        //* Do Post Info By Line
        add_action( 'gsfc_before_post_content', array( 'GS_Featured_Content', 'do_byline' ), 5 );
        add_action( 'gsfc_post_content', array( 'GS_Featured_Content', 'do_byline' ), 2 );
        add_action( 'gsfc_after_post_content', array( 'GS_Featured_Content', 'do_byline' ) );
        
        //* Do widget post content
        add_action( 'gsfc_post_content', array( 'GS_Featured_Content', 'do_post_content' ) );
        
        //* Do after widget post content
        add_action( 'gsfc_after_post_content', array( 'GS_Featured_Content', 'do_post_meta' ) );
        
        //* Do after loop
        add_action( 'gsfc_endwhile', array( 'GS_Featured_Content', 'do_posts_nav' ) );
        
        //* Do after loop reset
        add_action( 'gsfc_after_loop_reset', array( 'GS_Featured_Content', 'do_extra_posts' ) );
        add_action( 'gsfc_after_loop_reset', array( 'GS_Featured_Content', 'do_more_from_category' ) );
        
        //* Admin Scripts
        add_action( 'admin_enqueue_scripts', array( 'GS_Featured_Content', 'admin_scripts' ) );
        add_action( 'admin_print_footer_scripts', array( 'GS_Featured_Content', 'admin_footer_script' ) );
        
        //* Frontend Scripts
        add_action( 'gsfc_before_widget', array( 'GS_Featured_Content', 'enqueue_style' ) );
    }
    
    /**
     * Whether current admin page is the widgets page.
     */
    public static function is_widgets_page() {
        if ( ! is_admin() ) return false;
        
        $screen = get_current_screen();
        if ( 'widgets' != $screen->base && 'widgets' != $screen->id ) return false;
        return true;
    }
    
    /**
     * Filters excerpt's more text.
     *
     * @param string $more_text Current excerpt more text.
     * @return string Maybe modified more text.
     */
    public static function excerpt_more( $more_text ) {
        if ( isset( GS_Featured_Content::$widget_instance['more_text'] ) && GS_Featured_Content::$widget_instance['more_text'] ) {
            return sprintf( '<a rel="nofollow" class="more-link" href="%s">%s</a>', get_permalink(), GS_Featured_Content::$widget_instance['more_text'], GS_Featured_Content::$widget_instance['more_text'] );
        }
        return $more_text;
    }
    
    /**
     * Adds all Widget's Actions at once for easy removal.
     *
     * @param int $length Current excerpt length.
     * @return int Maybe new excerpt length.
     */
    public static function excerpt_length( $length ) {
        if ( GS_Featured_Content::has_value( 'excerpt_limit' ) && 0 != (int)GS_Featured_Content::$widget_instance['excerpt_limit'] )
            return (int)GS_Featured_Content::$widget_instance['excerpt_limit'];
        return $length;
    }
    
    /**
     * Adds all Widget's Actions at once for easy removal.
     */
    public static function enqueue_style( $instance ) {
        if ( is_admin() ) return;
        
        if ( empty( $instance['add_column_classes'] ) ) return; 
        $suffix = ( defined( 'WP_DEBUG' ) || defined( 'SCRIPT_DEBUG' ) ) ? '.css' : '.min.css';
        $deps    = defined( 'CHILD_THEME_NAME' ) && CHILD_THEME_NAME ? sanitize_title_with_dashes( CHILD_THEME_NAME ) : 'child-theme';
        wp_enqueue_style( 'gsfc-column-classes', plugins_url( GSFC_PLUGIN_NAME . '/css/column-classes' . $suffix ), array( $deps, ), GSFC_PLUGIN_VERSION );
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
        
        //* No BG Class
        if ( GS_Featured_Content::has_value( 'use_icon' ) )
            $classes[] = 'no-bg';
            
        //* Custom Class
        if ( GS_Featured_Content::has_value( 'class' ) )
            $classes[] = GS_Featured_Content::$widget_instance['class'];
        
        //* Column Class
        if ( GS_Featured_Content::has_value( 'column_class' ) )
            $classes[] = GS_Featured_Content::$widget_instance['column_class'];
            
        //* Replace Genesis Widgets
        if ( apply_filters( 'gs_replace_genesis', false ) )
            $classes[] = 'featured-post';

        return $classes;
    }
    
    /**
     * Inserts Post Image
     *
     * @param array $instance The settings for the particular instance of the widget.
     */
    public static function do_byline( $instance ) {
        if ( empty( $instance['show_byline'] ) || empty( $instance['post_info'] ) ) {
			return;
		}
        
        $byline = '';
        if ( !empty( $instance['post_info'] ) ) {
            $byline = sprintf( '<p class="byline post-info">%s</p>', do_shortcode( $instance['post_info'] ) );
		}
        
        GS_Featured_Content::maybe_echo( $instance, 'gsfc_before_post_content', 'byline_position', 'before-title', $byline );
        GS_Featured_Content::maybe_echo( $instance, 'gsfc_post_content', 'byline_position', 'after-title', $byline );
    }
    
    /**
     * Add attributes for entry image element shown in a widget.
     *
     * @since 2.0.0
     *
     * @global WP_Post $post Post object.
     *
     * @param array $attributes Existing attributes.
     *
     * @return array Amended attributes.
     */
    public static function attributes_gsfc_entry_image_widget( $attributes ) {

        global $post;

        $attributes['class']    = sprintf( 'entry-image attachment-%s %s', $post->post_type, $attributes['align'] );
        unset( $attributes['align'] );
        $attributes['itemprop'] = 'image';

        return $attributes;

    }
    
    /**
     * Inserts Post Image
     *
     * @param array $instance The settings for the particular instance of the widget.
     */
    public static function do_post_image( $instance ) {
        //* Bail if empty show param
        if ( empty( $instance['show_image'] ) ) {
            return;
        }

        $align = $instance['image_alignment'] ? esc_attr( $instance['image_alignment'] ) : 'alignnone';
        $link  = $instance['link_image_field'] ? $instance['link_image_field'] : get_permalink();
        $link  = '' !== genesis_get_custom_field( 'gsfc_link_image_field' ) ? genesis_get_custom_field( 'gsfc_link_image_field' ) : $link;
        
        $image = genesis_get_image( array(
				'format'  => 'html',
				'size'    => $instance['image_size'],
				'context' => 'featured-post-widget',
				'attr'    => genesis_parse_attr( 'gsfc-entry-image-widget', array( 'align' => $align, ) ),
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
        if ( 'gs_before_loop' == $name ) {
            _deprecated_argument( 'GS_Featured_Content::action', '1.1.5', __( 'Please use gsfc_before_loop hook.','gsfc' ) );
        }
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
        $link = $instance['link_title'] && $instance['link_title_field'] && genesis_get_custom_field( 'link_title_field' ) ? genesis_get_custom_field( 'link_title_field' ) : get_permalink();
        
        //* Add Link to Title?
        $wrap_open = $instance['link_title'] == 1 ? sprintf( '<a href="%s" title="%s">', $link, the_title_attribute( 'echo=0' ) ) : '';
        $wrap_close = $instance['link_title'] == 1 ? '</a>' : '';

        if ( ! empty( $instance['title_limit'] ) )
            $title = genesis_truncate_phrase( the_title_attribute( 'echo=0' ) , $instance['title_limit'] ) . $instance['title_cutoff'];
        else
            $title = the_title_attribute( 'echo=0' );
        
        if ( genesis_html5() ) {
            $hclass = apply_filters( 'gsfc_entry_title_class', ' class="entry-title"' );
        } else {
            $hclass = '';
		}

        $pattern = apply_filters( 'gsfc_post_title_pattern', '<h2%s>%s%s%s</h2>' );
        printf( $pattern, $hclass, $wrap_open, $title, $wrap_close );
    }
    
    /**
     * Outputs the selected content option if any
     *
     * @param array $instance The settings for the particular instance of the widget.
     */
    public static function do_post_content( $instance ) {
        //* Bail if empty show param
        if ( empty( $instance['show_content'] ) ) {
            return;
        }

        if ( '' !== $instance['show_content'] && ( $pre = apply_filters( 'gsfc_post_content_add_entry_content', false ) ) ) {
            echo '<div class="entry-content">';
        }
        switch ( $instance['show_content'] ) {
            case 'excerpt':
                add_filter( 'excerpt_more', array( 'GS_Featured_Content', 'excerpt_more' ) );
                the_excerpt();
                remove_filter( 'excerpt_more', array( 'GS_Featured_Content', 'excerpt_more' ) );
                break;
            case 'content-limit':
                the_content_limit( ( int ) $instance['content_limit'], esc_html( $instance['more_text'] ) );
                break;
            case 'content':
                the_content( esc_html( $instance['more_text'] ) );
                break;
            default:
                do_action( 'gsfc_show_content' );
                break;
        }
        if ( '' !== $instance['show_content'] && ( $pre = apply_filters( 'gsfc_post_content_add_entry_content', false ) ) ) {
            echo '</div>';
        }
        
    }

    /**
     * Outputs post meta if option is selected and anything is in the post meta field
     *
     * @param array $instance The settings for the particular instance of the widget.
     */
    public static function do_post_meta( $instance ) {
        if ( ! empty( $instance['show_archive_line'] ) && ! empty( $instance['post_meta'] ) )
            printf( '<p class="post-meta">%s</p>', do_shortcode( $instance['post_meta'] ) );
    }
    
    /**
     * Form submit script.
     */
    public static function admin_footer_script() { 
        if ( ! GS_Featured_Content::is_widgets_page() ) return; ?>
<script type="text/javascript">
function gsfcSave(t) {
    wpWidgets.save( jQuery(t).closest('div.widget'), 0, 1, 0 );
}
</script>
    <?php
    }
    
    /**
     * Form submit script.
     */
    public static function admin_scripts() {
        if ( ! GS_Featured_Content::is_widgets_page() ) return;
        $min = ( defined( 'WP_DEBUG' ) || defined( 'SCRIPT_DEBUG' ) ) ? '.' : '.min.';
        // wp_enqueue_script( 'gsfc-admin-widget', plugins_url( GSFC_PLUGIN_NAME . '/js/gsfc-admin' . $min . 'js' ), array( 'jquery', ), GSFC_PLUGIN_VERSION );
        
        wp_enqueue_style( 'gsfc-admin-widget', plugins_url( GSFC_PLUGIN_NAME . '/css/gsfc-admin' . $min . 'css' ), null, GSFC_PLUGIN_VERSION );
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
    
    /**
     * Sanitizies transient name (to less than 40 characters)
     * 
     * @param string $name Transient name. 
     * @return string $name Maybe modified transient name.
     */
    public static function sanitize_transient( $name ) {
        if ( 40 < strlen( $name ) )
            $name = substr( $name, 0, 40 );
        return $name;
    }
    
    /**
     * Gets transient with multisite support.
     * Due to multisite support, forces name < 40 chars
     * 
     * @param string $name Transient name.
     */
    protected static function get_transient( $name ) {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG || apply_filters( 'gsfc_debug', false ) ) {
            GS_Featured_Content::delete_transient( $name );
            return false;
        }
        
        $name = GS_Featured_Content::sanitize_transient( $name );
        return get_transient( $name );
    }
	
	/**
	 * WP.com's VIP get_term by. Get all Term data from database by Term field and data.
	 *
	 * Warning: $value is not escaped for 'name' $field. You must do it yourself, if
	 * required.
	 *
	 * The default $field is 'id', therefore it is possible to also use null for
	 * field, but not recommended that you do so.
	 *
	 * If $value does not exist, the return value will be false. If $taxonomy exists
	 * and $field and $value combinations exist, the Term will be returned.
	 *
	 * @since 1.1.3
	 *
	 * @uses get_term_by()
	 * @uses wp_cache_get()
	 * @uses wp_cache_set()
	 *
	 * @param string $field Either 'slug', 'name', 'id' (term_id), or 'term_taxonomy_id'
	 * @param string|int $value Search for this term value
	 * @param string $taxonomy Taxonomy Name
	 * @param string $output Constant OBJECT, ARRAY_A, or ARRAY_N
	 * @param string $filter Optional, default is raw or no WordPress defined filter will applied.
	 * @return mixed Term Row from database. Will return false if $taxonomy does not exist or $term was not found.
	 */
	public static function get_term_by( $field, $value, $taxonomy, $output = OBJECT, $filter = 'raw' ) {
		// ID lookups are cached
		if ( 'id' == $field ) {
			return get_term_by( $field, $value, $taxonomy, $output, $filter );
		}

		$cache_key = $field . '_' . md5( $value );
		$term_id = wp_cache_get( $cache_key, 'get_term_by' );

		if ( false === $term_id ) {
			$term = get_term_by( $field, $value, $taxonomy );
			if ( $term && ! is_wp_error( $term ) )
				wp_cache_set( $cache_key, $term->term_id, 'get_term_by' );
			else
				wp_cache_set( $cache_key, 0, 'get_term_by' ); // if we get an invalid value, let's cache it anyway
		} else {
			$term = get_term( $term_id, $taxonomy, $output, $filter );
		}

		if ( is_wp_error( $term ) ) {
			$term = false;
		}

		return $term;
	}
    
    /**
     * Sanitizes name & sets transient.
     * 
     * @param string $name Transient name.
     * @param mixed $value Transient value/data.
     * @param int $time Time to store transient (default: 1 day)
     */
    protected static function set_transient( $name, $value, $time = 86400 ) {
        $name = GS_Featured_Content::sanitize_transient( $name );
        set_transient( $name, $value, $time );
    }
    
    /**
     * Deletes transient with multisite support.
     * 
     * @param string $name Transient name.
     */
    protected static function delete_transient( $name ) {
        $name = GS_Featured_Content::sanitize_transient( $name );
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
            $term = GS_Featured_Content::get_term_by( 'slug', $posts_term['1'], $taxonomy );
            $link = $instance['archive_link'] ? $instance['archive_link'] : esc_url( get_term_link( $posts_term['1'], $taxonomy ) );
			printf(
				'<p class="more-from-%1$s"><a href="%2$s" title="%3$s">%4$s</a></p>',
                $taxonomy,
				$link,
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
        if ( empty( $instance['extra_posts'] ) || empty( $instance['extra_num'] ) ) return;
        global $wp_query, $_genesis_displayed_ids;
        
        $before_title = $instance['widget_args']['before_title'];
        $after_title  = $instance['widget_args']['after_title'];
        
        if ( ! empty( $instance['extra_title'] ) )
            echo GS_Featured_Content::build_tag( $before_title ) . esc_html( $instance['extra_title'] ) . $after_title;;

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
            )
        );
        
        $extra_posts_args = apply_filters( 'gsfc_extra_post_args', $extra_posts_args, $instance );
        
        if ( !empty( $instance['optimize'] ) && !empty( $instance['custom_field'] ) ) {
            if ( ! empty( $instance['delete_transients'] ) )
                GS_Featured_Content::delete_transient( 'gsfc_extra_' . $instance['custom_field'] );
            if ( false === ( $gsfc_query = GS_Featured_Content::get_transient( 'gsfc_extra_' . $instance['custom_field'] ) ) ) {
                $gsfc_query = new WP_Query( $extra_posts_args );
                $time = !empty( $instance['transients_time'] ) ? (int)$instance['transients_time'] : 60 * 60 * 24;
                GS_Featured_Content::set_transient( 'gsfc_extra_' . $instance['custom_field'], $gsfc_query, $time );
            }
        } else {
            $gsfc_query = new WP_Query( $extra_posts_args );
        }
        
        $optitems = $listitems = '';
        $items = array();
        
        if ( $gsfc_query->have_posts() ) :
            GS_Featured_Content::action( 'gsfc_before_list_items', $instance );
            while ( $gsfc_query->have_posts() ) : $gsfc_query->the_post();
                $_genesis_displayed_ids[] = $id = get_the_ID();
                $listitems .= sprintf( '<li><a href="%s" title="%s">%s</a></li>', get_permalink(), the_title_attribute( 'echo=0' ), get_the_title() );
                $optitems  .= sprintf( '<option class="%s" value="%s">%s</option>', $id, get_permalink(), get_the_title() );
                $items[] = get_post();
                
            endwhile;
            wp_reset_postdata();

            if ( strlen( $listitems ) > 0 && ( 'drop_down' != $instance['extra_format'] ) )
                echo apply_filters( 'gsfc_list_items', sprintf( '<%1$s>%2$s</%1$s>', $instance['extra_format'], $listitems ), $instance, $listitems, $items );
            elseif ( strlen( $optitems ) > 0 ) {
                printf(
                    '<select id="gsfc-%1$s-extras" onchange="window.location=document.getElementById(\'gsfc-%1$s-extras\').value;"><option value="none">%2$s</option>%3$s</select>',
                    $instance['custom_field'],
                    __( 'Select', 'gsfc' ),
                    $optitems
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
        $filters = array( '', 'attachment', 'soliloquy', );
        $filters = apply_filters( 'gsfc_exclude_post_types', $filters, GS_Featured_Content::$widget_instance );
        return( !in_array( $type, $filters ) );
    }
    
    /**
     * Obtains available post types
     *
     * @param string $type 'post_type' being tested
     * @return string
     */
    public static function get_post_types( $type = 'names', $args = array(), $operator = 'and' ) {
        $defaults = array(
            'public' => true
        );
        $args = wp_parse_args( $args, $defaults );
        $post_types = get_post_types( $args, $type, $operator );
        $post_types = array_filter( $post_types, array( __CLASS__, 'exclude_post_types' ) );
        return $post_types;
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
        $pt_obj = get_post_type_object( GS_Featured_Content::$widget_instance['post_type'] );
        $box   = array(
            'widget_title_link'     => array(
                'label'       => __( 'Link Title?', 'gsfc' ),
                'description' => '',
                'type'        => 'checkbox',
                'requires'    => '',
            ),
            'widget_title_link_href' => array(
                'label'       => __( 'Link', 'gsfc' ),
                'description' => __( 'Please include the entire link.', 'gsfc' ),
                'type'        => 'text',
                'requires'    => array(
                    'widget_title_link',
                    '',
                    true
                ),
            ),
        );
        $box_1 = array(
            'post_type'               => array(
                'label'       => __( 'Content Type', 'gsfc' ),
                'description' => '',
                'type'        => 'post_type_select',
                'requires'    => '',
            ),
            'page_id'                 => array(
                'label'       => __( 'Page', 'gsfc' ),
                'description' => '',
                'type'        => 'page_select',
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
                'requires'    => array(
                    'post_type',
                    'page',
                    true
                ),
            ),
            'include_exclude'         => array(
                'label'       => __( 'Include/Exclude', 'gsfc' ),
                'description' => '',
                'type'        => 'select',
                'options'     => array(
                    ''        => __( 'Select', 'gsfc' ),
                    'include' => __( 'Include', 'gsfc' ),
                    'exclude' => __( 'Exclude', 'gsfc' ),
                ),
                'requires'    => array(
                    'post_type',
                    'page',
                    true
                ),
            ),
            'post_id'                 => array(
                'label'       => sprintf( '<span class="gs-post-type-label">%s</span>', $pt_obj->name ) . ' ' . __( 'ID', 'gsfc' ),
                'description' => '',
                'type'        => 'text',
                'requires'    => array(
                    'include_exclude',
                    '',
                    true
                ),
            ),
            'posts_num'               => array(
                'label'       => sprintf( '%s %s %s', __( 'Number of', 'gsfc' ), $pt_obj->label, __( 'to Show', 'gsfc' ) ),
                'description' => '',
                'type'        => 'text_small',
                'requires'    => array(
                    'post_type',
                    'page',
                    true
                ),
            ),
            'posts_offset'            => array(
                'label'       => sprintf( '%s %s %s', __( 'Number of', 'gsfc' ), $pt_obj->label, __( 'to Offset', 'gsfc' ) ),
                'description' => '',
                'type'        => 'text_small',
                'requires'    => array(
                    'post_type',
                    'page',
                    true
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
                'requires'    => array(
                    'post_type',
                    'page',
                    true
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
                'requires'    => array(
                    'post_type',
                    'page',
                    true
                ),
            ),
            'meta_key'               => array(
                'label'       => __( 'Meta Key', 'gsfc' ),
                'description' => '',
                'type'        => 'text',
                'requires'    => array(
                    'orderby',
                    array( 'meta_value', 'meta_value_num', ),
                    false
                ),
            ),
            'paged'                   => array(
                'label'       => __( 'Work with Pagination', 'gsfc' ),
                'description' => '',
                'type'        => 'checkbox',
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
                'requires'    => array(
                    'post_type',
                    'page',
                    true
                ),
            ),
            'exclude_displayed'         => array(
                'label'       => __( 'Exclude Previously Displayed Posts?', 'gsfc' ),
                'description' => '',
                'type'        => 'checkbox',
                'requires'    => '',
            ),
        );
        
        $box_2 = array(
            'show_gravatar'           => array(
                'label'       => __( 'Show Author Gravatar', 'gsfc' ),
                'description' => '',
                'type'        => 'checkbox',
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
                'requires'    => array(
                    'show_gravatar',
                    '',
                    true
                ),
            ),
            'link_gravatar'          => array(
                'label'       => __( 'Link Gravatar', 'gsfc' ),
                'description' => '',
                'type'        => 'select',
                'options'     => array(
                    ''            => __( 'Do not link gravatar'  , 'gsfc' ),
                    'archive'     => __( 'Link to author archive', 'gsfc' ),
                    'website'     => __( 'Link to author website', 'gsfc' ),
                ),
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
                'requires'    => array(
                    'show_gravatar',
                    '',
                    true
                ),
            ),
        );
        
        $box_3 = array(
            'class'                 => array(
                'label'       => __( 'Class', 'gsfc' ),
                'description' => __( 'Fill in this field if you want to add a custom post class.', 'gsfc' ),
                'type'        => 'text',
                'requires'    => '',
            ),
            'add_column_classes'     => array(
                'label'       => __( 'Need to add column classes?', 'gsfc' ),
                'description' => 'Check to add column classes to your site.',
                'type'        => 'checkbox',
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
                'requires'    => '',
            ),
        );
        
        $box_4 = array(
            'optimize'               => array(
                'label'       => __( 'Optimize?', 'gsfc' ),
                'description' => 'Check to optimize WP_Query & enable site transients for the query results. Instance Identification Field must be filled in, which will be auto-populated based on your widget title.',
                'type'        => 'checkbox',
                'requires'    => '',
            ),
            'optimize_more_1' => array(
                'description' => 'Your main widget transient id: gsfc_main_' . GS_Featured_Content::$widget_instance['custom_field'],
                'type'        => 'description',
                'requires'    => array(
                    'optimize',
                    '',
                    true
                ),
            ),
            'optimize_more_2' => array(
                'description' => 'Your extra posts transient id: gsfc_extra_' . GS_Featured_Content::$widget_instance['custom_field'],
                'type'        => 'description',
                'requires'    => array(
                    'optimize',
                    '',
                    true
                ),
            ),
            'delete_transients'      => array(
                'label'       => __( 'Delete Transients?', 'gsfc' ),
                'description' => '',
                'type'        => 'checkbox',
                'requires'    => '',
            ),
            'transients_time'         => array(
                'label'       => __( 'Set Transients Expiration (seconds)', 'gsfc' ),
                'description' => '',
                'type'        => 'text',
                'requires'    => '',
            ),
            'custom_field'            => array(
                'label'       => __( 'Instance Identification Field', 'gsfc' ),
                'description' => __( 'Fill in this field if you need to test against an $instance value not included in the form', 'gsfc' ),
                'type'        => 'text',
                'requires'    => '',
            ),
        );
        
        $box_5 = array(
            'show_image'              => array(
                'label'       => __( 'Show Featured Image', 'gsfc' ),
                'description' => '',
                'type'        => 'checkbox',
                'requires'    => '',
            ),
            'link_image'              => array(
                'label'       => __( 'Image Link', 'gsfc' ),
                'description' => '',
                'type'        => 'select',
                'options'     => array(
                    '1' => __( 'Link Image to Post', 'gsfc' ),
                    '2' => __( 'Don\'t Link Image' , 'gsfc' ),
                ),
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
                'requires'    => array(
                    'link_image',
                    '1',
                    false
                ),
            ),
            'image_size'              => array(
                'label'       => __( 'Image Size', 'gsfc' ),
                'description' => '',
                'type'        => 'select',
                'options'     => GS_Featured_Content::get_image_size_options(),
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
                'requires'    => array(
                    'show_image',
                    '',
                    true
                ),
            ),
            'image_alignment'         => array(
                'label'       => __( 'Image Alignment', 'gsfc' ),
                'description' => '',
                'type'        => 'select',
                'options'     => array(
                    ''            => __( 'None'  , 'gsfc' ),
                    'alignleft'   => __( 'Left'  , 'gsfc' ),
                    'alignright'  => __( 'Right' , 'gsfc' ),
                    'aligncenter' => __( 'Center', 'gsfc' ),
                ),
                'requires'    => array(
                    'show_image',
                    '',
                    true
                ),
            ),
        );
        
        //* Box 2
        $box_6 = array(
            'show_title'              => array(
                'label'       => __( 'Show Post Title', 'gsfc' ),
                'description' => '',
                'type'        => 'checkbox',
                'requires'    => '',
            ),
            'title_limit'             => array(
                'label'       => __( 'Limit title to', 'gsfc' ),
                'description' => __( ' characters', 'gsfc' ),
                'type'        => 'text_small',
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
                'requires'    => array(
                    'show_title',
                    '',
                    true
                ),
            ),
            'link_title'              => array(
                'label'       => __( 'Link Title', 'gsfc' ),
                'description' => '',
                'type'        => 'select',
                'options'     => array(
                    '1' => __( 'Link Title to Post', 'gsfc' ),
                    '2' => __( 'Don\'t Link Title' , 'gsfc' ),
                ),
                'requires'    => array(
                    'show_title',
                    '',
                    true
                ),
            ),
            'link_title_field'              => array(
                'label'       => __( 'Link (Defaults to Permalink)', 'gsfc' ),
                'description' => '',
                'type'        => 'text',
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
                'requires'    => '',
            ),
            'byline_position'         => array(
                'label'       => __( 'Post Info Placement', 'gsfc' ),
                'description' => '',
                'type'        => 'select',
                'options'     => array(
                    'before-title'  => __( 'Before Title' , 'gsfc' ),
                    'after-title'   => __( 'After Title'  , 'gsfc' ),
                ),
                'requires'    => array(
                    'show_byline',
                    '',
                    true
                ),
            ),
            'post_info'               => array(
                'label'       => __( 'Post Info', 'gsfc' ),
                'description' => '',
                'type'        => 'text',
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
                'requires'    => '',
            ),
            'content_limit'           => array(
                'label'       => __( 'Limit content to', 'gsfc' ),
                'description' => __( ' characters', 'gsfc' ),
                'type'        => 'text_small',
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
                'requires'    => array(
                    'post_type',
                    'page',
                    true
                ),
            ),

            'post_meta'               => array(
                'label'       => __( 'Post Meta', 'gsfc' ),
                'description' => '',
                'type'        => 'text',
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
                'requires'    => '',
            ),
        );
        
        $box_7 = array(
            'extra_posts'             => array(
                'label'       => __( 'Display List of Additional Posts', 'gsfc' ),
                'description' => '',
                'type'        => 'checkbox',
                'requires'    => array(
                    'page_id',
                    '',
                    false
                ),
            ),
            'extra_title'             => array(
                'label'       => __( 'Title', 'gsfc' ),
                'description' => '',
                'type'        => 'text',
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
                'requires'    => array(
                    'extra_posts',
                    '',
                    true
                ),
            ),
            'more_from_category'      => array(
                'label'       => __( 'Show Category Archive Link', 'gsfc' ),
                'description' => '',
                'type'        => 'checkbox',
                'requires'    => array(
                    'posts_term',
                    '',
                    true
                ),
            ),
            'more_from_category_text' => array(
                'label'       => __( 'Link Text', 'gsfc' ),
                'description' => '',
                'type'        => 'text',
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
                'requires'    => array(
                    'more_from_category',
                    '',
                    true
                ),
            ),
        );
        
        $columns = array(
            'col'  => array( $box, ),
            'col1' => array(
                $box_1,
                $box_2,
                $box_3,
                $box_4,
            ),
            'col2' => array(
                $box_5,
                $box_6,
                $box_7,
            ),
        );
        return apply_filters( 'gsfc_form_fields', $columns, GS_Featured_Content::$widget_instance, compact( "box_1", "box_2", "box_3", "box_4", "box_5", "box_6", "box_7" ) );
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
	 * Get a list of registered taxonomy objects.
	 *
	 * @package WordPress
	 * @subpackage Taxonomy
	 * @since 3.0.0
	 * @uses $wp_taxonomies
	 * @see register_taxonomy
	 *
	 * @param array $args An array of key => value arguments to match against the taxonomy objects.
	 * @param string $output The type of output to return, either taxonomy 'names' or 'objects'. 'names' is the default.
	 * @param string $operator The logical operation to perform. 'or' means only one element
	 *  from the array needs to match; 'and' means all elements must match. The default is 'and'.
	 * @return array A list of taxonomy names or objects
	 */
	protected static function get_taxonomies( $args = array(), $output = 'names', $operator = 'and' ) {
		
        $cache_key  = 'gsfc_get_tax_' . md5( GS_Featured_Content::$widget_instance['widget']->id );
		$taxonomies = wp_cache_get( $cache_key, 'get_taxonomies' );

		if ( false === $taxonomies || null === $taxonomies ) {
			$taxonomies = get_taxonomies( $args, $output, $operator );
			if ( $taxonomies && ! is_wp_error( $taxonomies ) ) {
				wp_cache_set( $cache_key, $taxonomies, 'get_taxonomies', apply_filters( 'gsfc_get_taxonomies_cache_expires', 0 ) );
			} else {
				// if we get an invalid value, let's cache it anyway
				wp_cache_set( $cache_key, array(), 'get_taxonomies', apply_filters( 'gsfc_get_taxonomies_cache_expires', 0 ) );
			}
		} else {
			$taxonomies = get_taxonomies( $args, $output, $operator );
		}

        return $taxonomies;
	}
    
    /**
     * Outputs the column fields.
     * 
     * @param array $instance Current settings.
     * @param array $columns Array of fields to output.
     * @param object $obj Current Widget Object. 
     */
    public static function do_columns( $instance, $columns, $obj ) {
        echo '<div class="gsfc-widget-body">';
        foreach( $columns as $column => $boxes ) {
			if( 'col1' == $column )
                $col_class = 'gsfc-left-box';
			elseif( 'col2' == $column )
                $col_class = 'gsfc-right-box';
            else
                $col_class = 'gsfc-wide-box';
            printf( '<div class="%s">', $col_class );
            
			foreach( $boxes as $box ) {
                $box_style = isset( $box['box_requires'] ) ? ' style="'. GS_Featured_Content::get_display_option( $instance, $box['box_requires'] ) .'"' : '';
                // $box_style = isset( $box['box_requires'] ) ? ' style="'. GS_Featured_Content::get_display_option( $instance, $box['box_requires'][0], $box['box_requires'][1], $box['box_requires'][2] ) .'"' : '';
				printf( '<div class="gsfc-box"%s>', $box_style );
            
				foreach( $box as $field_id => $args ) {
                    if ( 'box_requires' == $field_id ) continue;
                    $data  = isset( $args['requires'] ) ? GS_Featured_Content::data_implode( $args['requires'] ) : '';
                    $style = '';
                   
                    if ( isset( $args['requires'] ) && is_array( $args['requires'] ) && 3 == count( $args['requires'] ) ) {
                        $style = ' style="'. GS_Featured_Content::get_display_option( $instance, $args['requires'] ) .'"';
                        echo '<div ' . $style . ' class="' . $args['type'] . ' ' . $field_id . '" data-requires-key="' . $args['requires'][0] . '" data-requires-value="' . $args['requires'][1] . '" >';
                    } else {
                        echo '<div ' . $style . ' class="' . $args['type'] . ' ' . $field_id . '" >';
                    }
                    
					switch( $args['type'] ) {
						case 'post_type_select' :
                            printf( '<label for="%1$s">%2$s</label><select onchange="gsfcSave(this)" id="%1$s" name="%3$s">',
                                $obj->get_field_id( $field_id ),
                                $args['label'],
                                $obj->get_field_name( $field_id )
                            );
                            
                            printf( '<option class="gs-pad-left-10" value="any" %s>%s</option>',
                                selected( esc_attr( $post_type ), $instance['post_type'], false ),
                                __( 'Any', 'gsfc' )
                            );
							
							$post_types = GS_Featured_Content::get_post_types();
							foreach ( $post_types as $post_type ) {
                                $pt = get_post_type_object( $post_type );
                                printf( '<option class="gs-pad-left-10" value="%s" %s>%s</option>',
                                    esc_attr( $post_type ),
                                    selected( esc_attr( $post_type ), $instance['post_type'], false ),
                                    esc_attr( $pt->label )
                                );
                            }
                                
							echo '</select>';
							break;

                        case 'page_select' :
                            printf( '<label for="%1$s">%2$s:</label><select id="%1$s" name="%3$s" onchange="gsfcSave(this)"><option value="" %4$s>%5$s</option>',
                                $obj->get_field_id( $field_id ),
                                $args['label'],
                                $obj->get_field_name( $field_id ),
                                selected( '', $instance['page_id'], false ),
                                esc_attr( __( 'Select page', 'gsfc' ) )
                            );

                            $pages = get_pages();
                            foreach ( $pages as $page )
                                printf( '<option class="gs-pad-left-10" value="%s" %s>%s</option>',
                                    esc_attr( $page->ID ),
                                    selected( esc_attr( $page->ID ), $instance['page_id'], false ),
                                    esc_attr( $page->post_title )
                                );
                                
							echo '</select>';
							break;
						
						case 'select_taxonomy' :
                            $taxonomies = GS_Featured_Content::get_taxonomies( apply_filters( 'gsfc_get_taxonomies_args', array( 'public' => true ), $instance, $obj ), 'objects' );
                        
                            $taxonomies = array_filter( (array)$taxonomies, array( 'GS_Featured_Content', 'exclude_taxonomies' ) );

                            printf( '<label for="%1$s">%2$s:</label><select id="%1$s" name="%3$s" onchange="gsfcSave(this)"><option value="" class="gs-pad-left-10" %4$s>%5$s</option>',
                                $obj->get_field_id( $field_id ),
                                $args['label'],
                                $obj->get_field_name( $field_id ),
                                selected( '', $instance['posts_term'], false ),
                                __( 'All Taxonomies and Terms', 'gsfc' )
                            );

                            foreach ( $taxonomies as $taxonomy ) {
                                $query_label = '';
                                if ( !empty( $taxonomy->query_var ) )
                                    $query_label = $taxonomy->query_var;
                                else
                                    $query_label = $taxonomy->name;
                                
                                echo '<optgroup label="'. esc_attr( $taxonomy->labels->name ) .'">
                                    <option class="gs-tax-optgroup" value="'. esc_attr( $query_label ) .'" '. selected( esc_attr( $query_label ), $instance['posts_term'], false ) .'>'. $taxonomy->labels->all_items .'</option>';
                                
                                $terms = get_terms( $taxonomy->name, 'orderby=name&hide_empty=1' );
                                
                                foreach ( $terms as $term )
                                    printf( '<option class="gs-pad-left-10" value="%s" %s>%s</option>',
                                        esc_attr( $query_label ) . ',' . $term->slug,
                                        selected( esc_attr( $query_label ) . ',' . $term->slug, $instance['posts_term'], false ),
                                        esc_attr( $term->name )
                                    );
                                    
                                echo '</optgroup>'; 
                            }
                            
                            echo '</select>';
							break;
							
						case 'text' :
							echo $args['description'] ? wpautop( $args['description'] ) : '';
                            printf( '<label for="%1$s">%2$s:</label>', $obj->get_field_id( $field_id ), $args['label'] );
                            printf( '<input type="text" id="%s" name="%s" value="%s" class="gs-widefat" />',
                                $obj->get_field_id( $field_id ),
                                $obj->get_field_name( $field_id ),
                                esc_attr( $instance[$field_id] )
                            );
							break;
						
						case 'text_small' :
                            printf( '<label for="%1$s">%2$s:</label>', $obj->get_field_id( $field_id ), $args['label'] );
                            printf( '<input type="text" class="gsfc-small" id="%s" name="%s" value="%s" />%s',
                                $obj->get_field_id( $field_id ),
                                $obj->get_field_name( $field_id ),
                                esc_attr( $instance[$field_id] ),
                                $args['description']
                            );
                            
							break;
							
						case 'select' :
                            printf( '<label for="%1$s">%2$s:</label><select id="%1$s" name="%3$s" onchange="gsfcSave(this)">',
                                $obj->get_field_id( $field_id ),
                                $args['label'],
                                $obj->get_field_name( $field_id )
                            );
                            
                            foreach( $args['options'] as $value => $label )
                                printf( '<option class="gs-pad-left-10" value="%s" %s>%s</option>',
                                        $value,
                                        selected( $value, $instance[$field_id], false ),
                                        $label
                                    );
                            
                            echo '</select>';
							break;
							
						case 'checkbox' :
                            printf( '<input type="checkbox" id="%1$s" name="%2$s" value="1" class="widget-control-save" %3$s />',
                                $obj->get_field_id( $field_id ),
                                $obj->get_field_name( $field_id ),
                                checked( 1, $instance[$field_id], false )
                                // $class
                            );
                            printf( '<label for="%1$s">%2$s</label>', $obj->get_field_id( $field_id ), $args['label'] );
                            echo $args['description'] ? wpautop( $args['description'] ) : '';
							break;
                        case 'p' :
                        case 'description' :
                            echo $args['description'] ? wpautop( $args['description'] ) : '';
                            break;
                        default:
                            do_action( 'gsfc_custom_field_' . $args['type'], $instance, $obj ); 
					}
                    echo '</div>';

				}
				
				echo '</div>';
			}
			
			echo '</div>';
				
		}
        echo '</div>';
    }
    
    /**
	 * Outputs the form fields.
	 *
	 * @uses do_columns()
	 *
	 * @param array $instance Current settings
	 * @param array $object Current GS_Featured_Content object
	 */
    public static function do_form_fields( $instance, $object ) {
        GS_Featured_Content::$widget_instance = array_merge( $instance, array( 'widget' => $object ) );

        //* Get Columns
        $columns = GS_Featured_Content::get_form_fields();
        GS_Featured_Content::do_columns( $instance, $columns, $object );
        
    }
    
	/**
	 * Echo the settings update form.
	 *
	 * @since 0.1.8
	 *
	 * @param array $instance Current settings
	 */
	public function form( $instance ) {
        
		//* Merge with defaults
		$instance = wp_parse_args( (array) $instance, $this->defaults );
        // GS_Featured_Content::$widget_instance = $instance;
        GS_Featured_Content::$widget_instance = array_merge( $instance, array( 'widget' => $this ) );
        
        //* Title Field
        echo '<p><label for="'. $this->get_field_id( 'title' ) .'">'. __( 'Title', 'gsfc' ) .':</label><input type="text" id="'. $this->get_field_id( 'title' ) .'" name="'. $this->get_field_name( 'title' ) .'" value="'. esc_attr( $instance['title'] ) .'" style="width:99%;" /></p>';
        
        do_action( 'gsfc_after_title_form_field', $instance, $this ); 
        do_action( 'gsfc_before_form_fields', $instance, $this ); 
        
        echo '<div class="gsfc-widget-wrapper">';
        
        do_action( 'gsfc_output_form_fields', $instance, $this );
        
        echo '</div>';
        
        do_action( 'gsfc_after_form_fields', $instance, $this ); 
        
    }
    
    /**
     * Returns "display: none;" if option and value match, or of they don't match with $standard is set to false
     *
     * @param array $instance Values set in widget isntance.
     * @param mixed $option Instance option to test.
     * @param mixed $value Value to test against.
     * @param boolean $standard Echo standard return false for oposite.
     */
    public static function get_display_option( $instance, $requires ) {
        $display  = '';
        $option   = $requires[0];
        $value    = $requires[1];
        $standard = $requires[2];

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
     * Implodes array to be key=>value string
     * 
     * @param array $array Array to implode.
     * 
     * @return string Imploded array.
     */
    public static function data_implode( $a ) {
        if ( is_array( $a ) && !empty( $a ) )
            return sprintf( ' data-requires-key="%s" data-requires-val="%s"', $a[0], $a[1] );
        else
            return '';
    }
    
    /**
	 * Sets custom field to a default.
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance Current settings
	 */
    public static function set_custom_field( $instance ) {
    
        $cf = isset( $instance['title'] ) ? sanitize_title_with_dashes( $instance['title'] ) : '';
        $cf = isset( $cf ) ? $cf : 'gsfc-' . $instance['post_type'];
        return $cf;
    }
    
    /**
     * Add class to $before_widget
     * 
     * @param string $b     Default $before_widget.
     * @param string $class Class to add to $before_widget.
     * 
     */
    public static function before_widget( $b, $class = '' ) {
    
        /* Add the width from $widget_width to the class from the $before widget */
        // no 'class' attribute - add one with the value of width
        if( strpos( $b, 'class' ) === false ) {
            $b = str_replace( '>', 'class="' . GS_Featured_Content::$base . '-' . sanitize_html_class( $class ) . ' featuredpost"', $b );
        }
        // there is 'class' attribute - append width value to it
        else {
            $b = str_replace( 'class="', 'class="'. GS_Featured_Content::$base . '-' . sanitize_html_class( $class ) . ' featuredpost ', $b );
        }
        
        /* Before widget */
        echo $b;
    }
   
    /**
     * Linkify widget title
     * 
     * @param string $widget_title 
     * @param array $instance The settings for the particular instance of the widget.
     * @param string $id_base ID base of the widget.
     * @return string Maybe modified widget title.
     */
    public function widget_title( $widget_title, $instance, $id_base ) {
        
        if ( isset( $instance['widget_title_link'] ) && isset( $instance['widget_title_link_href'] ) && $instance['widget_title_link_href'] )
            return apply_filters( 'gsfc_widget_title_link', sprintf( '<a href="%s">%s</a>', $instance['widget_title_link_href'], $widget_title ), $widget_title, $instance, $id_base );
            
        return $widget_title;
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

        GS_Featured_Content::$widget_instance = array_merge( $instance, array( 'widget_args' => $args, ) );
		global $wp_query, $_genesis_displayed_ids, $gs_counter;

		extract( $args );
        $instance['widget_args'] = $args;
        
        //* Add current page ID
        $_genesis_displayed_ids[] = get_the_ID();
        
		//* Merge with defaults
		$instance = wp_parse_args( (array) $instance, $this->defaults );

        do_action( 'gsfc_before_widget', $instance );
        GS_Featured_Content::before_widget( $before_widget, $instance['custom_field'] );
        add_filter( 'post_class', array( 'GS_Featured_Content', 'post_class' ) );
        
        if ( ! empty( $instance['posts_offset'] ) && ! empty( $instance['paged'] ) )
            add_filter( 'post_limits', array( 'GS_Featured_Content', 'post_limit' ) );
        else
            remove_filter( 'post_limits', array( 'GS_Featured_Content', 'post_limit' ) );

		//* Set up the author bio
		if ( ! empty( $instance['title'] ) ) {
            do_action( 'gsfc_before_widget_title', $instance );
			echo $before_title . apply_filters( 'gsfc_widget_title', apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ), $instance, $this->id_base ) . $after_title;
            do_action( 'gsfc_after_widget_title', $instance );
        }
        
        $q_args = array();
        
        //* Page ID
        if ( ! empty( $instance['page_id'] ) )
            $q_args['page_id'] = $instance['page_id'];
        
        //* Term Args
        $posts_term = array();
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
        
        //* Exclude displayed IDs from this loop?
		if ( ! empty( $instance['exclude_displayed'] ) ) {
            if ( isset( $q_args['post__not_in'] ) && is_array( $q_args['post__not_in'] ) )
                $q_args['post__not_in'] = array_unique( array_merge( $q_args['post__not_in'], (array) $_genesis_displayed_ids ) );
            else
                $q_args['post__not_in'] = (array) $_genesis_displayed_ids;
        }
        
        //* Before Loop Action
        if ( has_filter( 'gs_before_loop' ) ) {
            GS_Featured_Content::action( 'gs_before_loop', $instance );
        }
        GS_Featured_Content::action( 'gsfc_before_loop', $instance );
        
        if ( 0 === $instance['posts_num'] ) return;
        
        //* Optimize Query
        if ( ! empty( $instance['optimize'] ) ) {
            $q_args['cache_results'] = false;
            if ( empty( $instance['paged'] ) && empty( $instance['show_paged']  ) )
                $q_args['no_found_rows'] = true;
        }
        
        $instance['q_args'] = $q_args;
        GS_Featured_Content::$widget_instance = $instance;
        $pt = 'any' == $instance['post_type'] ? GS_Featured_Content::get_post_types() : $instance['post_type'];
        $query_args = array_merge(
            $q_args,
            array(
                'post_type'      => $pt, 
                'posts_per_page' => $instance['posts_num'], 
                'orderby'        => $instance['orderby'], 
                'order'          => $instance['order'], 
                'meta_key'       => $instance['meta_key'], 
                'paged'          => $page ,
            ) 
        );
        $instance['query_args'] = $query_args;
        GS_Featured_Content::$widget_instance = $instance;
        
        $query_args = apply_filters( 'gsfc_query_args', $query_args, $instance );
        
        // get transient
		if ( !empty( $instance['optimize'] ) && !empty( $instance['custom_field'] ) ) {
            if ( ! empty( $instance['delete_transients'] ) ) {
                GS_Featured_Content::delete_transient( 'gsfc_main_' . $instance['custom_field'] );
            }
            
            // Get transient, set transient if transient does not exist
            if ( false === ( $gsfc_query = GS_Featured_Content::get_transient( 'gsfc_main_' . $instance['custom_field'] ) ) ) {
                $gsfc_query = new WP_Query( $query_args );
                $time = !empty( $instance['transients_time'] ) ? $instance['transients_time'] : 60 * 60 * 24;
                GS_Featured_Content::set_transient( 'gsfc_main_' . $instance['custom_field'], $gsfc_query, $time );
            }
            else {
                $gsfc_query = apply_filters( 'gsfc_query_results', $gsfc_query, $instance );
            }
        } else {
            $gsfc_query = apply_filters( 'gsfc_query_results', new WP_Query( $query_args ) );
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
		$new_instance['custom_field']   = $new_instance['custom_field'] ? sanitize_title_with_dashes( $new_instance['custom_field'] ) : GS_Featured_Content::set_custom_field( $new_instance );
        
        GS_Featured_Content::delete_transient( 'gsfc_extra_' . $new_instance['custom_field'] );
        if ( $new_instance['custom_field'] != $old_instance['custom_field'] ) {
            GS_Featured_Content::delete_transient( 'gsfc_extra_' . $old_instance['custom_field'] );
        }
            
        GS_Featured_Content::delete_transient( 'gsfc_main_' . $new_instance['custom_field'] );
        if ( $new_instance['custom_field'] != $old_instance['custom_field'] ) {
            GS_Featured_Content::delete_transient( 'gsfc_main_' . $old_instance['custom_field'] );
        }
        
        // Fix potential issues
        $new_instance['page_id']         = 'page' !== $new_instance['post_type'] ? '' : absint( $new_instance['page_id'] );
        $new_instance['include_exclude'] = 'page' !== $new_instance['post_type'] ? $new_instance['include_exclude'] : '';
        $new_instance['link_title_field'] = $new_instance['link_title'] ? $new_instance['link_title_field'] : '';

		return apply_filters( 'gsfc_update', $new_instance, $old_instance );

	}
    
}
}
