<?php
if( !defined( 'WPINC' ) ){
    die;
}
/**
 * @Packge     : Security Companion
 * @Version    : 1.0
 * @Author     : Colorlib
 * @Author URI : http://colorlib.com/wp/
 *
 */



// Make sure the same class is not loaded twice in free/premium versions.
if ( !class_exists( 'Security_El_Widgets' ) ) {
    /**
     * Main Security Elementor Widgets Class
     *
     *
     * @since 1.7.0
     */
    final class Security_El_Widgets {
        /**
         * Security Companion Core Version
         *
         * Holds the version of the plugin.
         *
         * @since 1.7.0
         * @since 1.7.1 Moved from property with that name to a constant.
         *
         * @var string The plugin version.
         */
        const  VERSION = '1.0' ;
        /**
         * Minimum Elementor Version
         *
         * Holds the minimum Elementor version required to run the plugin.
         *
         * @since 1.7.0
         * @since 1.7.1 Moved from property with that name to a constant.
         *
         * @var string Minimum Elementor version required to run the plugin.
         */
        const  MINIMUM_ELEMENTOR_VERSION = '1.7.0';
        /**
         * Minimum PHP Version
         *
         * Holds the minimum PHP version required to run the plugin.
         *
         * @since 1.7.0
         * @since 1.7.1 Moved from property with that name to a constant.
         *
         * @var string Minimum PHP version required to run the plugin.
         */
        const  MINIMUM_PHP_VERSION = '5.4' ;
        /**
         * Instance
         *
         * Holds a single instance of the `Press_Elements` class.
         *
         * @since 1.7.0
         *
         * @access private
         * @static
         *
         * @var Press_Elements A single instance of the class.
         */
        private static  $_instance = null ;

        /**
         * Instance
         *
         * Ensures only one instance of the class is loaded or can be loaded.
         *
         * @since 1.7.0
         *
         * @access public
         * @static
         *
         * @return Press_Elements An instance of the class.
         */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /**
         * Clone
         *
         * Disable class cloning.
         *
         * @since 1.7.0
         *
         * @access protected
         *
         * @return void
         */
        public function __clone() {
            // Cloning instances of the class is forbidden
            _doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'security' ), '1.7.0' );
        }

        /**
         * Wakeup
         *
         * Disable unserializing the class.
         *
         * @since 1.7.0
         *
         * @access protected
         *
         * @return void
         */
        public function __wakeup() {
            // Unserializing instances of the class is forbidden.
            _doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'security' ), '1.7.0' );
        }

        /**
         * Constructor
         *
         * Initialize the security elementor widgets.
         *
         * @since 1.7.0
         *
         * @access public
         */
        public function __construct() {
           
            $this->init_hooks();
            do_action( 'press_elements_loaded' );
        }


        /**
         * Init Hooks
         *
         * Hook into actions and filters.
         *
         * @since 1.7.0
         *
         * @access private
         */
        private function init_hooks() {
            add_action( 'init', [ $this, 'init' ] );
        }


        /**
         * Init Security Elementor Widget
         *
         * Load the plugin after Elementor (and other plugins) are loaded.
         *
         * @since 1.0.0
         * @since 1.7.0 The logic moved from a standalone function to this class method.
         *
         * @access public
         */
        public function init() {

            if ( !did_action( 'elementor/loaded' ) ) {
                add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
                return;
            }

            // Check for required Elementor version

            if ( !version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
                add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
                return;
            }

            // Check for required PHP version

            if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
                add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
                return;
            }

            // Add new Elementor Categories
            add_action( 'elementor/elements/categories_registered', [ $this, 'add_elementor_category' ] );
            add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'register_widget_styles' ] );
            add_action( 'elementor/frontend/after_register_styles', [ $this, 'register_widget_styles' ] );
            add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'register_widget_styles' ] );

            // Register New Widgets
            add_action( 'elementor/widgets/widgets_registered', [ $this, 'on_widgets_registered' ] );

            // Security Companion enqueue style and scripts
            add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_element_widgets_scripts' ] );

        }

        /**
         * Admin notice
         *
         * Warning when the site doesn't have Elementor installed or activated.
         *
         * @since 1.1.0
         * @since 1.7.0 Moved from a standalone function to a class method.
         *
         * @access public
         */
        public function admin_notice_missing_main_plugin() {
            $message = sprintf(
            /* translators: 1: Elementor */
                esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'security' ),
                '<strong>' . esc_html__( 'Security Theme', 'security' ) . '</strong>',
                '<strong>' . esc_html__( 'Elementor', 'security' ) . '</strong>'
            );
            printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
        }

        /**
         * Admin notice
         *
         * Warning when the site doesn't have a minimum required Elementor version.
         *
         * @since 1.1.0
         * @since 1.7.0 Moved from a standalone function to a class method.
         *
         * @access public
         */
        public function admin_notice_minimum_elementor_version() {
            $message = sprintf(
            /* translators: 1: Elementor 2: Required Elementor version */
                esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'security' ),
                '<strong>' . esc_html__( 'Security', 'security' ) . '</strong>',
                '<strong>' . esc_html__( 'Elementor', 'security' ) . '</strong>',
                self::MINIMUM_ELEMENTOR_VERSION
            );
            printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
        }

        /**
         * Admin notice
         *
         * Warning when the site doesn't have a minimum required PHP version.
         *
         * @access public
         */
        public function admin_notice_minimum_php_version() {
            $message = sprintf(
            /* translators: 1: PHP 2: Required PHP version */
                esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'security' ),
                '<strong>' . esc_html__( 'Security', 'security' ) . '</strong>',
                '<strong>' . esc_html__( 'PHP', 'security' ) . '</strong>',
                self::MINIMUM_PHP_VERSION
            );
            printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
        }

        /**
         * Add new Elementor Categories
         *
         * Register new widget categories for Security widgets.
         *
         * @access public
         */
        public function add_elementor_category() {

            \Elementor\Plugin::instance()->elements_manager->add_category( 'security-elements', [
                'title' => __( 'Security Elements', 'security' ),
            ], 1 );

        }

        /**
         * Enqueue Widgets Scripts
         *
         * Enqueue custom scripts required to run security elementor widgets.
         *
         * @access public
         */
        public function enqueue_element_widgets_scripts() {

            // googlr map api key
            $apiKey  = security_opt('security_map_apikey');


            /******************
                Enqueue Css
            ******************/
            wp_enqueue_style( 'owl-carousel', SECURITY_COMPANION_DIR_URL . 'inc/elementor-widgets/assets/css/owl.carousel.css', array(), '1.0.0', 'all' );
            wp_enqueue_style( 'justifiedGallery', SECURITY_COMPANION_DIR_URL . 'inc/elementor-widgets/assets/css/justifiedGallery.min.css', array(), '3.7.0', 'all' );

            /*****************
                Enqueue Js
            ******************/

            // googleapis js
            wp_register_script( 'maps-googleapis', '//maps.googleapis.com/maps/api/js?key='.esc_attr( $apiKey ) );

            // ajaxchimp js
            wp_enqueue_script( 'jquery-ajaxchimp', SECURITY_COMPANION_DIR_URL . 'inc/elementor-widgets/assets/js/jquery.ajaxchimp.min.js', array('jquery'), '1.0', true );

            // jquery waypoints js
            wp_enqueue_script( 'waypoints', SECURITY_COMPANION_DIR_URL . 'inc/elementor-widgets/assets/js/waypoints.min.js', array('jquery'), '1.0', true );

            // jquery counterup js
            wp_enqueue_script( 'jquery-counterup', SECURITY_COMPANION_DIR_URL . 'inc/elementor-widgets/assets/js/jquery.counterup.min.js', array('jquery'), '1.0', true );

            // owl carousel js
            wp_enqueue_script( 'owl-carousel', SECURITY_COMPANION_DIR_URL . 'inc/elementor-widgets/assets/js/owl.carousel.min.js', array('jquery'), '1.0', true );

            // security map custom js
            wp_register_script( 'security-map-custom', SECURITY_COMPANION_DIR_URL . 'inc/elementor-widgets/assets/js/map-custom.js', array('jquery'), '1.0', true );

            wp_enqueue_script( 'justifiedGallery', SECURITY_COMPANION_DIR_URL . 'inc/elementor-widgets/assets/js/jquery.justifiedGallery.min.js', array('jquery'), '1.0', true );

            // security companion main js
            wp_enqueue_script( 'security', SECURITY_COMPANION_DIR_URL . 'inc/elementor-widgets/assets/js/security-companion-main.js', array('jquery'), '1.0', true );
           
        }

        /**
         * Register Widget Styles
         *
         * Register custom styles required to run Security.
         *
         * @access public
         */
        public function register_widget_styles() {
            // Typing Effect
            wp_enqueue_style( 'security-companion-elementor-edit', SECURITY_COMPANION_DIR_URL . 'inc/elementor-widgets/assets/css/elementor-edit.css' );
        }


        /**
         * Register New Widgets
         *
         * Include Security Companion widgets files and register them in Elementor.
         *
         * @since 1.0.0
         * @since 1.7.1 The method moved to this class.
         *
         * @access public
         */
        public function on_widgets_registered() {
            $this->include_widgets();
            $this->register_widgets();
        }

        /**
         * Include Widgets Files
         *
         * Load security companion widgets files.
         *
         * @since 1.0.0
         * @since 1.7.1 The method moved to this class.
         *
         * @access private
         */
        private function include_widgets() {
            
            require_once SECURITY_COMPANION_EW_DIR_PATH . 'widgets/banner.php';
            require_once SECURITY_COMPANION_EW_DIR_PATH . 'widgets/section-heading.php';
            require_once SECURITY_COMPANION_EW_DIR_PATH . 'widgets/counter.php';
            require_once SECURITY_COMPANION_EW_DIR_PATH . 'widgets/blog.php';
            require_once SECURITY_COMPANION_EW_DIR_PATH . 'widgets/about.php';
            require_once SECURITY_COMPANION_EW_DIR_PATH . 'widgets/contact.php';
            require_once SECURITY_COMPANION_EW_DIR_PATH . 'widgets/services.php';
            require_once SECURITY_COMPANION_EW_DIR_PATH . 'widgets/gallery.php';
            require_once SECURITY_COMPANION_EW_DIR_PATH . 'widgets/call-to-action.php';
            require_once SECURITY_COMPANION_EW_DIR_PATH . 'widgets/features.php';
            require_once SECURITY_COMPANION_EW_DIR_PATH . 'widgets/team-member.php';
            require_once SECURITY_COMPANION_EW_DIR_PATH . 'widgets/pricing-table.php';

        }

        /**
         * Register Widgets
         *
         * Register security companion widgets.
         *
         * @since 1.0.0
         * @since 1.7.1 The method moved to this class.
         *
         * @access private
         */
        private function register_widgets() {
            //  Register elements widgets   
            \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Securityelementor\Widgets\Security_Banner() ); 
            \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Securityelementor\Widgets\Security_sh() );
            \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Securityelementor\Widgets\Security_Blog() ); 
            \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Securityelementor\Widgets\Security_About() ); 
            \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Securityelementor\Widgets\Security_Contact() ); 
            \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Securityelementor\Widgets\Security_Services() );
            \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Securityelementor\Widgets\Security_Gallery() ); 
            \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Securityelementor\Widgets\Security_Cta() );
            \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Securityelementor\Widgets\Security_Features() );
            \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Securityelementor\Widgets\Security_Counter() ); 
            \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Securityelementor\Widgets\Security_Team_Member() );
            \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Securityelementor\Widgets\Security_Pricing() );

        }

    }
}
// Make sure the same function is not loaded twice in free/premium versions.



if ( !function_exists( 'security_el_widgets_load' ) ) {
    /**
     * Load Security elementor widget
     *
     * Main instance of Press_Elements.
     *
     * @since 1.0.0
     * @since 1.7.0 The logic moved from this function to a class method.
     */
    function security_el_widgets_load() {
        return Security_El_Widgets::instance();
    }

    // Run security elementor widget
    security_el_widgets_load();
}


add_action( 'wp_enqueue_scripts', function() {
    wp_dequeue_style('elementor-global');
});