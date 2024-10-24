<?php
/**
 * Plugin Name: Travel Packages
 * Description: A custom post type for Travel Packages.
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Register Custom Post Type
function create_travel_packages_post_type() {
    $args = array(
        'labels' => array(
            'name' => 'Travel Packages',
            'singular_name' => 'Travel Package',
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'thumbnail'),
    );

    register_post_type('travel_package', $args);
}
add_action('init', 'create_travel_packages_post_type');

// Add custom fields
function add_travel_package_meta_boxes() {
    add_meta_box('travel_package_details', 'Travel Package Details', 'travel_package_meta_box_callback', 'travel_package', 'normal', 'high');
}

function travel_package_meta_box_callback($post) {
    $price = get_post_meta($post->ID, '_travel_package_price', true);
    $availability = get_post_meta($post->ID, '_travel_package_availability', true);
    echo '<label for="travel_package_price">Price:</label>';
    echo '<input type="text" id="travel_package_price" name="travel_package_price" value="' . esc_attr($price) . '" />';
    echo '<label for="travel_package_availability">Availability:</label>';
    echo '<input type="text" id="travel_package_availability" name="travel_package_availability" value="' . esc_attr($availability) . '" />';
}

add_action('add_meta_boxes', 'add_travel_package_meta_boxes');

// Save the custom fields
function save_travel_package_meta($post_id) {
    if (isset($_POST['travel_package_price'])) {
        update_post_meta($post_id, '_travel_package_price', sanitize_text_field($_POST['travel_package_price']));
    }
    if (isset($_POST['travel_package_availability'])) {
        update_post_meta($post_id, '_travel_package_availability', sanitize_text_field($_POST['travel_package_availability']));
    }
}
add_action('save_post', 'save_travel_package_meta');

// Shortcode to display travel packages
function display_travel_packages() {
    $args = array('post_type' => 'travel_package', 'posts_per_page' => -1);
    $query = new WP_Query($args);
    $output = '<div class="travel-packages">';

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $price = get_post_meta(get_the_ID(), '_travel_package_price', true);
            $availability = get_post_meta(get_the_ID(), '_travel_package_availability', true);
            $output .= '<div class="travel-package">';
            $output .= '<h2>' . get_the_title() . '</h2>';
            $output .= '<div>' . get_the_content() . '</div>';
            $output .= '<p><strong>Price:</strong> ' . esc_html($price) . '</p>';
            $output .= '<p><strong>Availability:</strong> ' . esc_html($availability) . '</p>';
            $output .= '</div>';
        }
        wp_reset_postdata();
    } else {
        $output .= '<p>No travel packages found.</p>';
    }
    $output .= '</div>';

    return $output;
}
add_shortcode('travel_packages', 'display_travel_packages');
