<?php
/*
Plugin Name: Cooperating Centers
Plugin URI: https://github.com/bireme/centers-wp-plugin/
Description: VHL Cooperating Centers Directory WordPress plugin
Author: BIREME/OPAS/OMS
Version: 2.0
Author URI: http://reddes.bvsalud.org/
*/

define('CC_VERSION', '2.0' );

define('CC_SYMBOLIC_LINK', false );
define('CC_PLUGIN_DIRNAME', 'cc' );

if(CC_SYMBOLIC_LINK == true) {
    define('CC_PLUGIN_PATH',  ABSPATH . 'wp-content/plugins/' . CC_PLUGIN_DIRNAME );
} else {
    define('CC_PLUGIN_PATH',  plugin_dir_path(__FILE__) );
}

define('CC_PLUGIN_DIR',   plugin_basename( CC_PLUGIN_PATH ) );
define('CC_PLUGIN_URL',   plugin_dir_url(__FILE__) );

require_once(CC_PLUGIN_PATH . '/settings.php');
require_once(CC_PLUGIN_PATH . '/template-functions.php');

if(!class_exists('CC_Plugin')) {
    class CC_Plugin {

        private $plugin_slug = 'centers';
        private $service_url = 'https://fi-admin-api.bvsalud.org/';

        /**
         * Construct the plugin object
         */
        public function __construct() {
            // register actions

            add_action( 'init', array(&$this, 'load_translation'));
            add_action( 'admin_menu', array(&$this, 'admin_menu'));
            add_action( 'plugins_loaded', array(&$this, 'plugin_init'));
            add_action( 'template_redirect', array(&$this, 'theme_redirect'));
            add_action( 'widgets_init', array(&$this, 'register_sidebars'));
            add_action( 'after_setup_theme', array(&$this, 'title_tag_setup'));
            add_filter( 'get_search_form', array(&$this, 'search_form'));
            add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array(&$this, 'settings_link') );
            add_filter( 'document_title_separator', array(&$this, 'title_tag_sep') );
            add_filter( 'document_title_parts', array(&$this, 'theme_slug_render_title'));
            add_filter( 'wp_title', array(&$this, 'theme_slug_render_wp_title'));
            add_action( 'wp_ajax_centers_show_more_clusters', array($this, 'centers_show_more_clusters'));
            add_action( 'wp_ajax_nopriv_centers_show_more_clusters', array($this, 'centers_show_more_clusters'));

        } // END public function __construct

        /**
         * Activate the plugin
         */
        public static function activate()
        {
            // Do nothing
        } // END public static function activate

        /**
         * Deactivate the plugin
         */
        public static function deactivate()
        {
            // Do nothing
        } // END public static function deactivate

        function load_translation(){
		    // load internal plugin translations
		    load_plugin_textdomain('cc', false,  CC_PLUGIN_DIR . '/languages');
            // load plugin translations
            $site_language = strtolower(get_bloginfo('language'));
            $lang = substr($site_language,0,2);
		}

		function plugin_init() {
		    $cc_config = get_option('cc_config');

		    if ( $cc_config && $cc_config['plugin_slug'] != ''){
		        $this->plugin_slug = $cc_config['plugin_slug'];
		    }

		}

		function admin_menu() {

		    add_submenu_page( 'options-general.php', __('Cooperating Centers Settings', 'cc'), __('Cooperating Centers', 'cc'), 'manage_options', 'cc', 'cc_page_admin');

		    //call register settings function
		    add_action( 'admin_init', array(&$this, 'register_settings') );

		}

		function theme_redirect() {
		    global $wp, $cc_service_url, $cc_plugin_slug;
		    $pagename = '';

            // check if request contains plugin slug string
            $pos_slug = strpos($wp->request, $this->plugin_slug);
            if ( $pos_slug !== false ){
                $pagename = substr($wp->request, $pos_slug);
            }

            if ( is_404() && $pos_slug !== false ){

                $cc_service_url = $this->service_url;
                $cc_plugin_slug = $this->plugin_slug;
                $similar_docs_url = $this->similar_docs_url;

                add_action( 'wp_enqueue_scripts', array(&$this, 'page_template_styles_scripts'));

                if ( $this->startsWith($pagename, $this->plugin_slug) ){

    		        if ($pagename == $this->plugin_slug){
    		            #$template = CC_PLUGIN_PATH . '/template/home.php';
    		            $template = CC_PLUGIN_PATH . '/template/results.php';
                    }elseif ($pagename == $this->plugin_slug . '/results'){
    		            $template = CC_PLUGIN_PATH . '/template/results.php';
    		        }else{
    		            $template = CC_PLUGIN_PATH . '/template/detail.php';
    		        }
    		        // force status to 200 - OK
    		        status_header(200);

    		        // redirect to page and finish execution
    		        include($template);
    		        die();
    		    }
            }
		}

		function register_sidebars(){
		    $args = array(
		        'name' => 'Cooperating Centers Sidebar',
		        'id'   => 'cc-home',
		        'description' => __('Cooperating Centers Area', 'cc'),
		        'before_widget' => '<section id="%1$s" class="row-fluid widget %2$s">',
		        'after_widget'  => '</section>',
		        'before_title'  => '<h2 class="widgettitle">',
		        'after_title'   => '</h2>',
		    );
		    register_sidebar( $args );

            $args2 = array(
                'name' => 'Cooperating Centers Header',
                'id'   => 'cc-header',
                'description' => __('Cooperating Centers Header', 'cc'),
                'before_widget' => '<section id="%1$s" class="row-fluid widget %2$s">',
                'after_widget'  => '</section>',
                'before_title'  => '<header class="row-fluid border-bottom marginbottom15"><h1 class="h1-header">',
                'after_title'   => '</h1></header>',
            );
            register_sidebar( $args2 );
		}

        function title_tag_sep(){
            return '|';
        }

        function theme_slug_render_title($title) {
            global $wp, $cc_plugin_title;
            $pagename = '';

            // check if request contains plugin slug string
            $pos_slug = strpos($wp->request, $this->plugin_slug);
            if ( $pos_slug !== false ){
                $pagename = substr($wp->request, $pos_slug);
            }

            if ( is_404() && $pos_slug !== false ){
                $cc_config = get_option('cc_config');
                if ( function_exists( 'pll_the_languages' ) ) {
                    $current_lang = pll_current_language();
                    $cc_plugin_title = $cc_config['plugin_title_' . $current_lang];
                }else{
                    $cc_plugin_title = $cc_config['plugin_title'];
                }
                $title['title'] = $cc_plugin_title . " | " . get_bloginfo('name');
            }

            return $title;
        }

        function theme_slug_render_wp_title($title) {
            global $wp, $cc_plugin_title;
            $pagename = '';

            // check if request contains plugin slug string
            $pos_slug = strpos($wp->request, $this->plugin_slug);
            if ( $pos_slug !== false ){
                $pagename = substr($wp->request, $pos_slug);
            }

            if ( is_404() && $pos_slug !== false ){
                $cc_config = get_option('cc_config');

                if ( function_exists( 'pll_the_languages' ) ) {
                    $current_lang = pll_current_language();
                    $cc_plugin_title = $cc_config['plugin_title_' . $current_lang];
                }else{
                    $cc_plugin_title = $cc_config['plugin_title'];
                }

                if ( $cc_plugin_title )
                    $title = $cc_plugin_title . ' | ';
                else
                    $title = '';
            }

            return $title;
        }

        function title_tag_setup() {
            add_theme_support('title-tag');
        }

		function page_title(){
		    global $wp;
		    $pagename = $wp->query_vars["pagename"];

		    if ( strpos($pagename, $this->plugin_slug) === 0 ) { //pagename starts with plugin slug
		        return __('Cooperating Centers', 'cc') . ' | ';
		    }
		}

		function search_form( $form ) {
		    global $wp;
		    $pagename = $wp->query_vars["pagename"];

		    if ($pagename == $this->plugin_slug || preg_match('/detail\//', $pagename)) {
		        $form = preg_replace('/action="([^"]*)"(.*)/','action="' . home_url($this->plugin_slug) . '"',$form);
		    }

		    return $form;
		}

        function page_template_styles_scripts(){
            wp_enqueue_style('bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css');
            wp_enqueue_style('style-centers', CC_PLUGIN_URL . 'template/css/style.css');
            wp_enqueue_style('accessibility', CC_PLUGIN_URL . 'template/css/accessibility.css');
            wp_enqueue_style('fontawesome', '//use.fontawesome.com/releases/v5.8.1/css/all.css');
            wp_enqueue_style('fontgoogle', '//fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,900');

            wp_enqueue_script('bootstrap', '//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js', array('jquery'), '4.0.0', true);
            wp_enqueue_script('cookie',  CC_PLUGIN_URL . 'template/js/cookie.js', array('jquery'), '1.0.0', true);
            wp_enqueue_script('accessibility', CC_PLUGIN_URL . 'template/js/accessibility.js', array('jquery'), '1.0.0', true);
            wp_enqueue_script('functions', CC_PLUGIN_URL . 'template/js/functions.js', array('jquery'), '1.0.0', true);


            wp_enqueue_script('jquery');

            wp_add_inline_script('jquery', 'const cc_script_vars = ' . json_encode( array(
                    'ajaxurl' => admin_url( 'admin-ajax.php' ),
                    'ajaxnonce' => wp_create_nonce( 'ajax_post_validation' )
            )), 'before');
        }

		function register_settings(){
            register_setting('cc-settings-group', 'cc_config');
            wp_enqueue_style ('cc', CC_PLUGIN_URL . 'template/css/admin.css');
            wp_enqueue_script('jquery-ui-sortable');
		}

        function settings_link($links) {
            $settings_link = '<a href="options-general.php?page=cc.php">Settings</a>';
            array_unshift($links, $settings_link);
            return $links;
        }

        function startsWith ($string, $startString) {
            $len = strlen($startString);
            return (substr($string, 0, $len) === $startString);
        }

        function centers_show_more_clusters() {
            global $cc_service_url;
            $cc_service_url = $this->service_url;

            ob_start();
            include(CC_PLUGIN_PATH . '/template/cluster.php');
            $contents = ob_get_contents();
            ob_end_clean();

            if ( $contents ) {
                echo $contents;
            } else {
                echo 0;
            }

            die();
        }

	} // END class CC_Plugin
} // END if(!class_exists('CC_Plugin'))

if(class_exists('CC_Plugin'))
{
    // Installation and uninstallation hooks
    register_activation_hook(__FILE__, array('CC_Plugin', 'activate'));
    register_deactivation_hook(__FILE__, array('CC_Plugin', 'deactivate'));

    // Instantiate the plugin class
    $wp_plugin_template = new CC_Plugin();
}

?>
