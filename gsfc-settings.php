<?php
/**
 * Genesis Featured Content Widget Settings.
 * Requires Genesis 1.8 or later
 *
 * This file registers all of this child theme's 
 * specific Theme Settings, accessible from
 * Genesis > Sandbox Settings.
 *
 * @category   Genesis_Featured_Content
 * @package    Admin
 * @subpackage Settings
 * @author     Travis Smith
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link       http://wpsmith.net/
 * @since      1.1.0
 */

/** Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) exit( 'Cheatin&#8217; uh?' );
 
/**
 * Adds a new metabox to Genesis Theme Settings Page.
 *
 * @category   Genesis_Sandbox_Featured_Content
 * @package    Admin
 * @author     Travis Smith
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link       http://wpsmith.net/
 * @since      1.1.0
 */
class GSFC_Settings extends Genesis_Admin_Settings {
    
    /**
     * Loads on proper hook, genesis_init.
     *
     * @since 1.1.0
     */
    public function __construct() {
        add_action( 'genesis_init', array( $this, 'load' ), 15 );
    }
    
    /**
     * Add GSFC Settings.
     *
     * @since 1.1.0
     */
    public function load() {
        
        /** Add GSFC Settings */
        remove_action( 'after_setup_theme', 'genesis_add_admin_menu' );
        add_action( 'after_setup_theme', array( $this, 'add_admin_menu' ) );
        add_action( 'genesis_admin_before_metaboxes', array( $this, 'add_metabox' ) );
        add_action( 'genesis_theme_settings_defaults', array( $this, 'add_defaults' ) );
    }
    
    /**
     * Add Genesis top-level item in admin menu.
     *
     * Calls the `genesis_admin_menu hook` at the end - all submenu items should be attached to that hook to ensure
     * correct ordering.
     *
     * @since 0.2.0
     *
     * @global \Genesis_Admin_Settings _genesis_admin_settings          Theme Settings page object.
     * @global string                  _genesis_theme_settings_pagehook Old backwards-compatible pagehook.
     *
     * @return null Returns null if Genesis menu is disabled, or disabled for current user
     */
    public function add_admin_menu() {
        if ( ! is_admin() )
            return;

        global $_genesis_admin_settings, $_gsfc_settings;

        if ( ! current_theme_supports( 'genesis-admin-menu' ) )
            return;

        //* Don't add menu item if disabled for current user
        $user = wp_get_current_user();
        if ( ! get_the_author_meta( 'genesis_admin_menu', $user->ID ) )
            return;
            
        parent::__construct();
        $_genesis_admin_settings = $_gsfc_settings;

        //* Set the old global pagehook var for backward compatibility
        global $_genesis_theme_settings_pagehook;
        $_genesis_theme_settings_pagehook = $_genesis_admin_settings->pagehook;

        do_action( 'genesis_admin_menu' );
    }
    
    /**
     * Adds GSFC Metabox to Theme Settings Page.
     *
     * @since 1.1.0
     */
    public function add_metabox() {
        if ( class_exists( 'Genesis_Featured_Widget_Amplified' ) )
            add_meta_box( 'gsfc-settings', __( 'Genesis Sandbox Featured Content Settings', 'gsfc' ), array( $this, 'settings' ), $this->pagehook, 'main', 'high' );
    }
    
    /**
     * Adds GSFC defaults to Genesis options.
     *
     * @since 1.1.0
     */
    public function add_defaults( $defaults ) {
        $defaults['gsfc_gfwa'] = 0;
        return $defaults;
    }
    
    /**
     * Outputs GSFC metabox markup contents.
     *
     * @since 1.1.0
     */
    public function settings() {
    ?>
        <label for="<?php echo $this->get_field_id( 'gsfc_gfwa' ); ?>"><input type="checkbox" name="<?php echo $this->get_field_name( 'gsfc_gfwa' ); ?>" id="<?php echo $this->get_field_id( 'gsfc_gfwa' ); ?>" value="1"<?php checked( $this->get_field_value( 'gsfc_gfwa' ) ); ?> />
        <?php _e( 'Have Genesis Sandbox Featured Content Widget take over Genesis Featured Widget Amplified?', 'gsfc' ); ?></label>
    <?php
    }
    
}