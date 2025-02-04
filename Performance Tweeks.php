<?php

// Load Styles and Scripts names in footer for dequeue 
/*
add_action('wp_footer', function() {
    if (is_admin()) return; // Prevent this from running in the WordPress admin area

    echo "<!-- Styles Loaded -->\n";
    global $wp_styles;
    foreach ($wp_styles->queue as $style) {
        echo "<!-- Style: " . $style . " -->\n";
    }

    echo "<!-- Scripts Loaded -->\n";
    global $wp_scripts;
    foreach ($wp_scripts->queue as $script) {
        echo "<!-- Script: " . $script . " -->\n";
    }
}, 999);

*/

// Disable Plugins, Scripts & Styles based on page rules
function disable_plugins_and_assets_conditionally($plugins) {
    // Get current URL
    $current_url = $_SERVER['REQUEST_URI'];

    // Define the specific plugins to disable
    $plugins_to_disable = [
        'advanced-coupons-for-woocommerce-free/advanced-coupons-for-woocommerce-free.php', // Adjust the path if needed
        'woocommerce-product-filters/woocommerce-product-filters.php', // Adjust the path if needed
		'woocommerce-additional-variation-images/woocommerce-additional-variation-images.php',
		'woocommerce-product-addons/woocommerce-product-addons.php',
		'woocommerce/woocommerce.php',
    ];

    // Check if the URL is under /shop-online/ or has the ?s= parameter
    if (strpos($current_url, '/shop-online/') === false && !isset($_GET['s'])) {
        // Remove the specified plugins
        foreach ($plugins_to_disable as $plugin) {
            if (isset($plugins[$plugin])) {
                unset($plugins[$plugin]);
            }
        }

        // Prevent their CSS and JS from loading
        add_action('wp_enqueue_scripts', function () {
            // List all known styles and scripts that the plugins load
            wp_dequeue_style('acfwf-wc-cart-block-integration'); // Replace with actual style handle
			wp_dequeue_style('acfwf-wc-checkout-block-integration'); // Replace with actual style handle
			wp_dequeue_style('acfw-blocks-frontend'); // Replace with actual style handle			
			wp_dequeue_style('wcpf-plugin-style'); // Replace with actual style handle		
			wp_dequeue_style('woocommerce-inline'); // Replace with actual style handle
			wp_dequeue_style('wcpf-plugin-style'); // Replace with actual style handle	
			wp_dequeue_style('brands-styles'); // Replace with actual style handle
			wp_dequeue_style('filebird-block-filebird-gallery-style'); // Replace with actual style handle
            wp_dequeue_script('woocommerce-addons'); // Replace with actual script handle
            wp_dequeue_script('woocommerce'); // Replace with actual script handle			
			wp_dequeue_script('wcpf-plugin-vendor-script'); // Replace with actual script handle
			wp_dequeue_script('wcpf-plugin-script'); // Replace with actual script handle
			wp_dequeue_script('avada-woo-product-variations'); // Replace with actual script handle
			wp_dequeue_script('wc_additional_variation_images_script'); // Replace with actual script handle
			wp_dequeue_script('wc-add-to-cart'); // Replace with actual script handle			
			wp_dequeue_script('avada-woocommerce'); // Replace with actual script handle	
			wp_dequeue_script('avada-woo-products'); // Replace with actual script handle
			wp_dequeue_script('avada-woo-product-images'); // Replace with actual script handle	
			
        }, 9999);
    }

    return $plugins;
}
add_filter('option_active_plugins', 'disable_plugins_and_assets_conditionally');



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




// Prevent Duplicate Post Slugs
function prevent_slug_duplicates( $slug, $post_ID, $post_status, $post_type, $post_parent, $original_slug ) {
$custom_post_type = "**insert custom post type here**"    
$check_post_types = array(
        'post',
        'page',
        $custom_post_type
    );
    
    if ( ! in_array( $post_type, $check_post_types ) ) {
        return $slug;
    }

    if ( $custom_post_type == $post_type ) {
        // Saving a custom_post_type post, check for duplicates in POST or PAGE post types
        $post_match = get_page_by_path( $slug, 'OBJECT', 'post' );
        $page_match = get_page_by_path( $slug, 'OBJECT', 'page' );

        if ( $post_match || $page_match ) {
            $slug .= '-2';
        }
    } else {
        // Saving a POST or PAGE, check for duplicates in custom_post_type post type
        $custom_post_type_match = get_page_by_path( $slug, 'OBJECT', $custom_post_type );

        if ( $custom_post_type_match ) {
            $slug .= '-2';
        }
    }

    return $slug;
}
add_filter( 'wp_unique_post_slug', 'prevent_slug_duplicates', 10, 6 );
// End Duplicate Post Slugs
