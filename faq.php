<?php
/**
 * Plugin Name: Custom FAQ
 * Description: A customizable FAQ system with admin interface and front-end display.
 * Version: 1.2
 * Author: Your Name
 * Text Domain: custom-faq
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class CustomFAQ {

    /**
     * Constructor to initialize the plugin
     */
    public function __construct() {
        // Register Custom Post Type
        add_action( 'init', array( $this, 'register_faq_cpt' ) );

        // Add Admin Menu for Settings
        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );

        // Register Settings
        add_action( 'admin_init', array( $this, 'register_settings' ) );

        // Remove global enqueue action
        // add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );

        // Register Shortcode
        add_shortcode( 'custom_faq', array( $this, 'render_faq_shortcode' ) );
    }

    /**
     * Register the FAQ Custom Post Type
     */
    public function register_faq_cpt() {
        $labels = array(
            'name'                  => _x( 'FAQs', 'Post Type General Name', 'custom-faq' ),
            'singular_name'         => _x( 'FAQ', 'Post Type Singular Name', 'custom-faq' ),
            'menu_name'             => __( 'FAQs', 'custom-faq' ),
            'name_admin_bar'        => __( 'FAQ', 'custom-faq' ),
            'archives'              => __( 'FAQ Archives', 'custom-faq' ),
            'attributes'            => __( 'FAQ Attributes', 'custom-faq' ),
            'parent_item_colon'     => __( 'Parent FAQ:', 'custom-faq' ),
            'all_items'             => __( 'All FAQs', 'custom-faq' ),
            'add_new_item'          => __( 'Add New FAQ', 'custom-faq' ),
            'add_new'               => __( 'Add New', 'custom-faq' ),
            'new_item'              => __( 'New FAQ', 'custom-faq' ),
            'edit_item'             => __( 'Edit FAQ', 'custom-faq' ),
            'update_item'           => __( 'Update FAQ', 'custom-faq' ),
            'view_item'             => __( 'View FAQ', 'custom-faq' ),
            'view_items'            => __( 'View FAQs', 'custom-faq' ),
            'search_items'          => __( 'Search FAQ', 'custom-faq' ),
            'not_found'             => __( 'Not found', 'custom-faq' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'custom-faq' ),
            'featured_image'        => __( 'Featured Image', 'custom-faq' ),
            'set_featured_image'    => __( 'Set featured image', 'custom-faq' ),
            'remove_featured_image' => __( 'Remove featured image', 'custom-faq' ),
            'use_featured_image'    => __( 'Use as featured image', 'custom-faq' ),
            'insert_into_item'      => __( 'Insert into FAQ', 'custom-faq' ),
            'uploaded_to_this_item' => __( 'Uploaded to this FAQ', 'custom-faq' ),
            'items_list'            => __( 'FAQs list', 'custom-faq' ),
            'items_list_navigation' => __( 'FAQs list navigation', 'custom-faq' ),
            'filter_items_list'     => __( 'Filter FAQs list', 'custom-faq' ),
        );
        $args = array(
            'label'                 => __( 'FAQ', 'custom-faq' ),
            'description'           => __( 'Frequently Asked Questions', 'custom-faq' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor' ),
            'hierarchical'          => false,
            'public'                => false,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 25,
            'menu_icon'             => 'dashicons-editor-help',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => false,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => false,
            'capability_type'       => 'post',
        );
        register_post_type( 'faq', $args );
    }

    /**
     * Add Settings Page under Settings menu
     */
    public function add_settings_page() {
        add_options_page(
            'Custom FAQ Settings',
            'Custom FAQ',
            'manage_options',
            'custom-faq',
            array( $this, 'render_settings_page' )
        );
    }

    /**
     * Register Settings
     */
    public function register_settings() {
        register_setting( 'custom_faq_settings_group', 'custom_faq_settings', array( $this, 'sanitize_settings' ) );

        add_settings_section(
            'custom_faq_section_general',
            __( 'General Settings', 'custom-faq' ),
            array( $this, 'section_general_callback' ),
            'custom-faq'
        );

        add_settings_field(
            'faq_section_title_color',
            __( 'FAQ Section Title Color', 'custom-faq' ),
            array( $this, 'faq_section_title_color_callback' ),
            'custom-faq',
            'custom_faq_section_general'
        );

        add_settings_field(
            'faq_question_color',
            __( 'FAQ Question Color', 'custom-faq' ),
            array( $this, 'faq_question_color_callback' ),
            'custom-faq',
            'custom_faq_section_general'
        );

        add_settings_field(
            'faq_answer_color',
            __( 'FAQ Answer Color', 'custom-faq' ),
            array( $this, 'faq_answer_color_callback' ),
            'custom-faq',
            'custom_faq_section_general'
        );
    }

    /**
     * Sanitize Settings Input
     */
    public function sanitize_settings( $input ) {
        $sanitized = array();

        if ( isset( $input['faq_section_title_color'] ) && $this->validate_hex_color( $input['faq_section_title_color'] ) ) {
            $sanitized['faq_section_title_color'] = sanitize_hex_color( $input['faq_section_title_color'] );
        }

        if ( isset( $input['faq_question_color'] ) && $this->validate_hex_color( $input['faq_question_color'] ) ) {
            $sanitized['faq_question_color'] = sanitize_hex_color( $input['faq_question_color'] );
        }

        if ( isset( $input['faq_answer_color'] ) && $this->validate_hex_color( $input['faq_answer_color'] ) ) {
            $sanitized['faq_answer_color'] = sanitize_hex_color( $input['faq_answer_color'] );
        }

        return $sanitized;
    }

    /**
     * Validate Hex Color
     */
    private function validate_hex_color( $color ) {
        return preg_match( '/^#[a-f0-9]{6}$/i', $color );
    }

    /**
     * General Settings Section Callback
     */
    public function section_general_callback() {
        echo '<p>' . __( 'Customize the appearance of the FAQ section.', 'custom-faq' ) . '</p>';
    }

    /**
     * FAQ Section Title Color Field Callback
     */
    public function faq_section_title_color_callback() {
        $options = get_option( 'custom_faq_settings' );
        $color  = isset( $options['faq_section_title_color'] ) ? esc_attr( $options['faq_section_title_color'] ) : '#E91E63';
        echo '<input type="color" id="faq_section_title_color" name="custom_faq_settings[faq_section_title_color]" value="' . $color . '" />';
    }

    /**
     * FAQ Question Color Field Callback
     */
    public function faq_question_color_callback() {
        $options = get_option( 'custom_faq_settings' );
        $color  = isset( $options['faq_question_color'] ) ? esc_attr( $options['faq_question_color'] ) : '#3B566E';
        echo '<input type="color" id="faq_question_color" name="custom_faq_settings[faq_question_color]" value="' . $color . '" />';
    }

    /**
     * FAQ Answer Color Field Callback
     */
    public function faq_answer_color_callback() {
        $options = get_option( 'custom_faq_settings' );
        $color  = isset( $options['faq_answer_color'] ) ? esc_attr( $options['faq_answer_color'] ) : '#6F8BA4';
        echo '<input type="color" id="faq_answer_color" name="custom_faq_settings[faq_answer_color]" value="' . $color . '" />';
    }

    /**
     * Render Settings Page
     */
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields( 'custom_faq_settings_group' );
                do_settings_sections( 'custom-faq' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Enqueue Scripts and Styles
     */
    public function enqueue_assets() {
        // Enqueue jQuery (already included with WordPress)
        wp_enqueue_script( 'jquery' );

        // Enqueue Custom JS for Accordion Functionality
        wp_enqueue_script( 'custom-faq-js', plugin_dir_url( __FILE__ ) . 'assets/js/custom-faq.js', array( 'jquery' ), '1.2', true );

        // Enqueue Custom CSS
        wp_enqueue_style( 'custom-faq-css', plugin_dir_url( __FILE__ ) . 'assets/css/custom-faq.css', array(), '1.2' );

        // Pass AJAX URL to JavaScript if needed in future
        wp_localize_script( 'custom-faq-js', 'customFaq', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
        ) );
    }

    /**
     * Render FAQ Shortcode
     */
    public function render_faq_shortcode( $atts ) {
        // Enqueue Scripts and Styles only when shortcode is used
        $this->enqueue_assets();

        // Shortcode attributes with defaults
        $atts = shortcode_atts( array(
            'title'          => 'FAQ',
            'initial_toggle' => 'closed', // Options: 'open' or 'closed'
            'ids'            => '',         // Comma-separated list of FAQ IDs
        ), $atts, 'custom_faq' );

        // Prepare query arguments
        $args = array(
            'post_type'      => 'faq',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        );

        // If 'ids' attribute is provided, filter the FAQs
        if ( ! empty( $atts['ids'] ) ) {
            // Sanitize and validate IDs
            $ids = array_map( 'intval', explode( ',', $atts['ids'] ) );
            $ids = array_filter( $ids, function( $id ) {
                return get_post_type( $id ) === 'faq';
            } );

            if ( ! empty( $ids ) ) {
                $args['post__in'] = $ids;
                $args['orderby'] = 'post__in'; // Maintain the order as provided
            }
        }

        // Query FAQs
        $faqs = new WP_Query( $args );

        if ( ! $faqs->have_posts() ) {
            return '<p>No FAQs found.</p>';
        }

        // Retrieve Settings
        $settings = get_option( 'custom_faq_settings' );
        $title_color   = isset( $settings['faq_section_title_color'] ) ? esc_html( $settings['faq_section_title_color'] ) : '#E91E63';
        $question_color = isset( $settings['faq_question_color'] ) ? esc_html( $settings['faq_question_color'] ) : '#3B566E';
        $answer_color  = isset( $settings['faq_answer_color'] ) ? esc_html( $settings['faq_answer_color'] ) : '#6F8BA4';

        // Determine initial toggle state
        $initial_toggle = strtolower( $atts['initial_toggle'] );
        $initial_toggle = in_array( $initial_toggle, array( 'open', 'closed' ) ) ? $initial_toggle : 'closed';

        // Start output buffering
        ob_start();
        ?>
        <section class="faq-section">
            <div class="container">
                <div class="row">
                    <!-- ***** FAQ Start ***** -->
                    <div class="col-md-8 offset-md-2">
                        <div class="faq-title text-center pb-3">
                            <h4 style="color: <?php echo $title_color; ?>;"><?php echo esc_html( $atts['title'] ); ?></h4>
                        </div>
                    </div>
                    <div class="col-md-8 offset-md-2">
                        <div class="faq" id="accordion">
                            <?php
                            $count = 1;
                            while ( $faqs->have_posts() ) : $faqs->the_post();
                                $faq_id = 'faq-' . get_the_ID();
                                $is_open = ( $initial_toggle === 'open' ) ? 'show' : '';
                                $aria_expanded = ( $initial_toggle === 'open' ) ? 'true' : 'false';
                                ?>
                                <div class="card">
                                    <div class="card-header" id="heading-<?php echo esc_attr( $faq_id ); ?>">
                                        <h5 class="faq-title" data-toggle="collapse" data-target="#collapse-<?php echo esc_attr( $faq_id ); ?>" aria-expanded="<?php echo $aria_expanded; ?>" aria-controls="collapse-<?php echo esc_attr( $faq_id ); ?>" style="color: <?php echo $question_color; ?>;">
                                            <span class="badge"><?php echo esc_html( $count ); ?></span><?php the_title(); ?>
                                        </h5>
                                    </div>
                                    <div id="collapse-<?php echo esc_attr( $faq_id ); ?>" class="collapse <?php echo esc_attr( $is_open ); ?>" aria-labelledby="heading-<?php echo esc_attr( $faq_id ); ?>" data-parent="#accordion">
                                        <div class="card-body" style="color: <?php echo $answer_color; ?>;">
                                            <?php the_content(); ?>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                $count++;
                            endwhile;
                            wp_reset_postdata();
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }
}

// Initialize the plugin
new CustomFAQ();