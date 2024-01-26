<?php
// Remove the current page from the Yoast breadcrumb trail, rendering the parent crumb as a link.
add_filter(
    'wpseo_breadcrumb_single_link',
    function ( $link_output ) {
        if ( strpos( $link_output, 'breadcrumb_last' ) !== false ) {
            $link_output = '';
        }
        return $link_output;
    }
);
