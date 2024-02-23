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

// Shortcode to get Yoast Target Term
function get_yoast_term_post_meta( ) {
	return get_post_meta(get_the_ID(), '_yoast_wpseo_focuskw', true );
}
add_shortcode( 'yoast_term', 'get_yoast_term_post_meta' );





// Shortcode for Getting Custom Field - Gets First Word 
function custom_field_type( ){
 $acf_relational_field = "**ENTER ACF RELATIONAL FIELD HERE**";      
 custom_field_type = strtok(get_field($acf_relational_field, false, false), " ");
   return $location_type_text;
}
add_shortcode( 'custom_field_shortcode', 'location_type' );


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
       $acf_relational_field = "**ENTER ACF RELATIONAL FIELD HERE**";
       $post_id = get_field($acf_relational_field, false, false);
       return get_post_meta($post_id, $field, true);
}
add_shortcode('relational_field', 'shortcode_rel_field');


// Shortcode Generator for Map on City Pages
function city_map_shortcode() {
$acf_relational_field = "**ENTER ACF RELATIONAL FIELD HERE**";
$post_id = get_field($acf_relational_field, false, false);	

$field_lookup = '**enter related object field to get**'	
$key_zip = get_post_meta($post_id,$field_lookup,true);

// Customize as needed - should be an existing shortcode that is modified   
$show_map = '[wpsl auto_locate="false" start_marker="red" start_location="'.$key_zip.'"]';
$map =  do_shortcode($show_map);

return $map;
	
}


// Shortcode Generator 
function custom_generated_shortcode() {

$post_id = get_field('details_store_locator', false, false);	
	
$key_zip = get_post_meta($post_id,'wpsl_zip',true);

$show_map = '[wpsl auto_locate="false" start_marker="red" start_location="'.$key_zip.'"]';
$map =  do_shortcode($show_map);

return $map;
	
}

add_shortcode('cutom_shortcode_gen', 'custom_generated_shortcode'); 
