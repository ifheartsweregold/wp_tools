<?php

// Shortcode for Location Type
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
// End All Fields Shortcode

// Shortcode Generator for Map on City Pages
function city_map_shortcode() {

$post_id = get_field('details_store_locator', false, false);	
	
$key_zip = get_post_meta($post_id,'wpsl_zip',true);

$show_map = '[wpsl auto_locate="false" start_marker="red" start_location="'.$key_zip.'"]';
$map =  do_shortcode($show_map);

return $map;
	
}

add_shortcode('city_map', 'city_map_shortcode'); 


// WPSL Widget Templates
add_filter( 'wpsl_widget_templates', 'custom_widget_templates' );

function custom_widget_templates( $templates ) {

    /**
     * The 'id' is used in the shortcode and accessible with [wpsl_widget template="the-used-id"]
     * The 'name' is shown in the widget configuration screen.
     * The 'path' points in this example to a 'wpsl-templates' folder inside your theme folder.
     * The 'file_name' is the name of the file that contains the custom template code.
     */
    $templates[] = array (
        'id'        => 'custom',
        'name'      => 'Custom template',
        'path'      => get_stylesheet_directory() . '/' . 'wpsl_templates/',
        'file_name' => 'keyper_custom.php'
    );

    return $templates;
}

// Custom Thumb for Results WPSL 

add_filter( 'wpsl_thumb_size', 'custom_thumb_size' );

function custom_thumb_size() {
    
    $size = array( 65, 65 );
    
    return $size;
}



