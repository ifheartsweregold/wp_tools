<?php
// Remove Gutenberg Block Library CSS from loading on the frontend
function smartwp_remove_wp_block_library_css(){
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );
    wp_dequeue_style( 'global-styles' );
	wp_dequeue_style( 'classic-theme-styles' );
	wp_dequeue_style( 'wc-block-style' ); // Remove WooCommerce block CSS
	wp_dequeue_style( 'storefront-gutenberg-blocks' ); // Storefront theme
} 

add_action('wp_enqueue_scripts', 'smartwp_remove_wp_block_library_css', 100 );

add_filter('use_block_editor_for_post', '__return_false',10);
add_filter('gutenberg_can_edit_post', '__return_false');
add_filter('use_widgets_block_editor', '__return_false' );
// End Guten Stuff

// Remove Comments Totally
add_action('admin_init', function () {
    // Redirect any user trying to access comments page
    global $pagenow;
     
    if ($pagenow === 'edit-comments.php') {
        wp_safe_redirect(admin_url());
        exit;
    }
 
    // Remove comments metabox from dashboard
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
 
    // Disable support for comments and trackbacks in post types
    foreach (get_post_types() as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
});
 
// Close comments on the front-end
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);
 
// Hide existing comments
add_filter('comments_array', '__return_empty_array', 10, 2);
 
// Remove comments page in menu
add_action('admin_menu', function () {
    remove_menu_page('edit-comments.php');
});
 
// Remove comments links from admin bar
add_action('init', function () {
    if (is_admin_bar_showing()) {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    }
});


// Automatically Delete Woocommerce Images After Deleting a Product
add_action( 'before_delete_post', 'delete_product_images', 10, 1 );

function delete_product_images( $post_id )
{
    $product = wc_get_product( $post_id );

    if ( !$product ) {
        return;
    }

    $featured_image_id = $product->get_image_id();
    $image_galleries_id = $product->get_gallery_image_ids();

    if( !empty( $featured_image_id ) ) {
        wp_delete_post( $featured_image_id );
    }

    if( !empty( $image_galleries_id ) ) {
        foreach( $image_galleries_id as $single_image_id ) {
            wp_delete_post( $single_image_id );
        }
    }
}


// Prevents unwanted thumbnail sizes - Use Thumbnail Generator Plug-in to see list of current sizes. This is not retroactive. 
add_filter( 'intermediate_image_sizes_advanced', function ( $sizes ) {
    $allowed = [ '2048x2048', '1536x1536', 'medium_large' ];
    foreach ( $sizes as $name => $size ) {
        if ( in_array( $name, $allowed ) ) {
            unset( $sizes[ $name ] );
        }
    }
    return $sizes;
} );


// Allow Revisions on Products - WooCommerce

add_filter( 'woocommerce_register_post_type_product', 'wpse_modify_product_post_type' );

function wpse_modify_product_post_type( $args ) {
     $args['supports'][] = 'revisions';

     return $args;
}

// Prevent Certain User Emails from seeing certain admin side bar menu items. 
function custom_menu_page_removing() {
    if ( get_currentuserinfo()->user_email != 'admin@keypermarketing.com' )
        remove_menu_page( 'avada-white-label-branding-admin' );
}
add_action( 'admin_menu', 'custom_menu_page_removing' );
