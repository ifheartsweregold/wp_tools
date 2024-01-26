<?php

// Add Custom Fields on Edit Screen
add_filter('acf/settings/remove_wp_meta_box', '__return_false');

// do shortcode in ACF custom fields
add_filter('acf/format_value/type=textarea', 'do_shortcode');
add_filter('acf/format_value/type=text', 'do_shortcode');


// Shortcode for Page Title
function page_title_sc( ){
   return get_the_title();
}
add_shortcode( 'page_title', 'page_title_sc' );


// Shortcode for Getting Custom Field
function location_type( ){
 $location_type_text = strtok(get_field('details_location_type', false, false), " ");
   return $location_type_text;
}
add_shortcode( 'one_word_location_type', 'location_type' );


// Creates all Custom Fields as Shortcode
function shortcode_field($atts){
     extract(shortcode_atts(array(
                  'post_id' => NULL,
               ), $atts));
  if(!isset($atts[0])) return;
       $field = esc_attr($atts[0]);
       global $post;
       $post_id = (NULL === $post_id) ? $post->ID : $post_id;
       return get_post_meta($post_id, $field, true);
}
add_shortcode('field', 'shortcode_field');
// End All Fields Shortcode

// Creates all Relational Custom Fields as Shortcode
function shortcode_rel_field($atts){
     extract(shortcode_atts(array(
                  'post_id' => NULL,
               ), $atts));
  if(!isset($atts[0])) return;
       $field = esc_attr($atts[0]);
       global $post;
       $post_id = get_field('details_store_locator', false, false);
       return get_post_meta($post_id, $field, true);
}
add_shortcode('relational_field', 'shortcode_rel_field');
