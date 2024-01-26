<?php

// Show All Post Meta on page 
add_action('wp_head', 'output_all_postmeta' );
function output_all_postmeta() {

	$postmetas = get_post_meta(get_the_ID());

	foreach($postmetas as $meta_key=>$meta_value) {
		echo $meta_key . ' : ' . $meta_value[0] . '<br/>';
	}
}
